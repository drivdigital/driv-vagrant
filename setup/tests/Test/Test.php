<?php

/**
 * Class Test
 */
abstract class Test {

  protected $totalCount = 0;

  protected $success = [];

  protected $fails = [];

  abstract public function run();

  /**
   * Assert
   * @param bool $expression
   * @param string $description
   */
  public function assert( $expression, $description, $comments = [] ) {
    if ( $expression ) {
      $this->success[] = $description;
      $output          = "✓ - " . $description;
    }
    else {
      $this->fails[] = $description;

      $output = "\e[1m";
      $output .= "✗ - " . $description;
      if ( ! empty( $comments ) ) {
        $output .= "\n        Comments: ";
        foreach ( $comments as $comment ) {
          $output .= "\n          - " . $comment;
        }
      }
      $output .= "\033[0m";
    }
    echo '    ' . $output . "\n";
    $this->totalCount++;
  }

  public function getResult() {
    return [
        'total_count' => $this->totalCount,
        'success'     => $this->success,
        'fails'       => $this->fails
    ];

  }

}
