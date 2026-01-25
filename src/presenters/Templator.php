<?php

namespace presenters;
use Exception;

/**
 * Represents a simple template compiler that parses custom template syntax
 * (like {if ...}, {= ...}, {/if}) and converts it into valid PHP code.
 */
class Templator
{
    // The raw content of the loaded template file
    private $templateContent = false;

    // The output file path where compiled content will be saved
    private $targetFile;

    // Error messages
    private $TEMPLATE_NOT_INCLUDED_ERROR = "No template loaded!\n";
    private $INVALID_EXPRESSION_ERROR = "Invalid expression!\n";
    private $INVALID_BRACKETING_ERROR = "Invalid bracketing!\n";

    // Supported template control structures
    private $FLAGS = ['if', 'foreach', 'for'];

    // Stack used to validate correct opening/closing of control structures
    private $stackForFlags = [];

    /**
     * Loads a template file and stores its contents in memory.
     *
     * @param string $fileName Path to the template file
     * @throws Exception if the file cannot be read
     */
    public function loadTemplate(string $fileName)
    {
        $content = @file_get_contents($fileName);
        if ($content === false) {
            throw new Exception($this->TEMPLATE_NOT_INCLUDED_ERROR);
        }
        $this->templateContent = $content;
    }

    /**
     * Compiles the loaded template and saves the result to the given file.
     *
     * @param string $fileName Output file path
     * @throws Exception if no template is loaded or mismatched braces are found
     */
    public function compileAndSave(string $fileName)
    {
        // Ensure a template has been loaded and the target is not a directory
        if ($this->templateContent === false || is_dir($fileName)) {
            throw new Exception($this->TEMPLATE_NOT_INCLUDED_ERROR);
        }

        $this->targetFile = $fileName;

        // Clear previous file content
        file_put_contents($this->targetFile, "");

        // Start the compilation process
        $this->compile($fileName);

        // After compilation, the stack should be empty (no unclosed blocks)
        if (count($this->stackForFlags) !== 0) {
            throw new Exception($this->INVALID_BRACKETING_ERROR);
        }
    }

    /**
     * Appends compiled content to the target file.
     *
     * @param string $content Content to write
     */
    private function saveContent(string $content)
    {
        file_put_contents($this->targetFile, $content, FILE_APPEND);
    }

    /**
     * Main compilation loop — iterates over the template and looks for `{` markers
     * that indicate expressions or control structures.
     *
     * @param string $fileName Output file path (not directly used in this method)
     */
    private function compile(string $fileName)
    {
        for ($i = 0; $i < strlen($this->templateContent); ++$i) {
            if ($this->templateContent[$i] === '{') {
                // Found a potential template expression or statement
                $this->checkForTemplate($i);
            } else {
                // Normal character, just output it as is
                $this->saveContent($this->templateContent[$i]);
            }
        }
    }

    /**
     * Checks whether the substring starting at the given index represents
     * a control structure ({if ...}, {foreach ...}, {for ...}) or expression ({= ...}).
     *
     * @param int &$index Reference to the current index in the template string
     */
    private function checkForTemplate(int &$index)
    {
        $substring = substr($this->templateContent, $index);

        $isFlag = false;

        // Try to parse control structures (if, for, foreach)
        foreach ($this->FLAGS as $flag) {
            $isFlag = $isFlag || $this->parseIfForForeach($substring, $index, $flag);
        }

        // If it's neither a control structure nor an expression, treat it as a literal "{"
        if (!($isFlag || $this->parseExpression($substring, $index))) {
            $this->saveContent('{');
        }
    }

    /**
     * Parses control structure blocks like {if ...}, {foreach ...}, {for ...} and {/if}, {/foreach}, {/for}.
     * Converts them into valid PHP opening and closing tags.
     *
     * @param string &$content The substring starting with '{'
     * @param int &$index Reference to current index in the template string
     * @param string $flag The control structure keyword ("if", "foreach", "for")
     * @return bool true if a valid control structure was parsed
     */
    private function parseIfForForeach(string &$content, int &$index, string $flag): bool
    {
        // Opening tag: e.g. {if condition}
        if (substr($content, 1, strlen($flag)) === $flag) {
            if (($endPosition = strpos($content, '}')) !== false) {
                // Extract the expression inside the brackets
                $content = substr($content, strlen($flag) + 1, $endPosition - strlen($flag) - 1);

                // Validate and move index forward
                $this->validateAndIncrementIndex($content, $index, $endPosition);

                // Convert to PHP code
                $content = "<?php " . $flag . " ( $content ) {  ?>";
                $this->saveContent($content);

                // Push the flag onto the stack to check proper closing later
                array_push($this->stackForFlags, $flag);
                return true;
            }
        }

        // Closing tag: e.g. {/if}
        if (substr($content, 1, strlen($flag) + 2) == '/' . $flag . '}') {
            $content = "<?php } ?>";
            $this->saveContent($content);
            $index += strlen($flag) + 2;

            // Ensure proper nesting: pop the last opened flag and match it
            if (count($this->stackForFlags) == 0 || array_pop($this->stackForFlags) !== $flag) {
                throw new Exception($this->INVALID_BRACKETING_ERROR);
            }
            return true;
        }

        return false;
    }

    /**
     * Parses output expressions like {= $variable } and escapes them.
     *
     * @param string &$content The substring starting with '{'
     * @param int &$index Reference to current index in the template string
     * @return bool true if a valid expression was parsed
     */
    private function parseExpression(string &$content, int &$index): bool
    {
        // Expression must start with "{="
        if ($content[1] === '=') {
            if (($endPosition = strpos($content, '}')) !== false) {
                // Extract expression between {= and }
                $content = substr($content, 2, $endPosition - 2);

                // Validate and increment index
                $this->validateAndIncrementIndex($content, $index, $endPosition);

                // Convert to safe PHP output (escaped)
                $content = "<?= htmlspecialchars($content) ?>";
                $this->saveContent($content);
                return true;
            }
        }
        return false;
    }

    /**
     * Validates expressions for syntax correctness and moves the current index forward.
     *
     * @param string &$expression The parsed expression content
     * @param int &$index Current index reference
     * @param int $difference_in_index How much to move the index forward
     * @throws Exception if the expression is empty or contains illegal braces
     */
    private function validateAndIncrementIndex(string &$expression, int &$index, int $difference_in_index)
    {
        // Expression cannot be empty or contain braces
        if (strlen(trim($expression)) === 0) {
            throw new Exception($this->INVALID_EXPRESSION_ERROR);
        }
        if (str_contains($expression, '{') or str_contains($expression, '}')) {
            throw new Exception($this->INVALID_EXPRESSION_ERROR);
        }

        // Advance index so the parser skips the parsed template part
        $index += $difference_in_index;
    }
}
