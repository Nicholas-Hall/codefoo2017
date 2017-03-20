<?php
    function newBoggleBoard($size) {
        // expected output of newBoggleBoard(3) is a 3 by 3 2d array
        $size = $size;

        $board = array();

        for ($row = 0; $row <= $size - 1; $row++) {
            $tempRow = array();
            for ($column = 0; $column <= $size - 1; $column++) {
                $tempRow[] = rand(0,9);
            }
            $board[] = $tempRow;
        }

        return $board;
    }

    function callSolveBoggle($boardToSolve){
        //callSolveBoggle recieves the boardToSolve and sends it to Solve boggle for every spot of a 3x3 array.

            $solutionsList = [];
            for ($row=0; $row < 3; $row++) {
                for ($column=0; $column < 3 ; $column++) {
                    $solutionsList = solveBoggle($boardToSolve,$row,$column,[],$solutionsList);
                }
            }
            return $solutionsList;
        }

    function sumGivenPostions($boardToSolve,$chain) {
        //sumGivenPostions recieves both a board and a chain being worked.
        // It returns the total if every spot in the chain was added up.
        $sum = 0;
        for ($i=0; $i < count($chain); $i++) {
            $x = $chain[$i][0];
            $y = $chain[$i][1];
            $sum += $boardToSolve[$x][$y];
        }
        // var_dump($chain,$sum);
        return $sum;
    }

    function safeBoggleMoves ($boardToSolve,$xpos,$ypos){
        //safe boggle moves recieves a board a the current x and y pos and appends to $safeMoves if that spot exists in the board.
        $safeMoves = [];

        if(isset($boardToSolve[$xpos-1][$ypos])){
            $safeMoves[] = [$xpos-1,$ypos];
        }

        if(isset($boardToSolve[$xpos][$ypos-1])){
            $safeMoves[] = [$xpos,$ypos-1];
        }

        if(isset($boardToSolve[$xpos-1][$ypos-1])){
            $safeMoves[] = [$xpos-1,$ypos-1];
        }

        if(isset($boardToSolve[$xpos+1][$ypos-1])){
            $safeMoves[] = [$xpos+1,$ypos-1];
        }

        if(isset($boardToSolve[$xpos+1][$ypos])){
            $safeMoves[] = [$xpos+1,$ypos];
        }

        if(isset($boardToSolve[$xpos+1][$ypos+1])){
            $safeMoves[] = [$xpos+1,$ypos+1];
        }

        if(isset($boardToSolve[$xpos][$ypos+1])){
            $safeMoves[] = [$xpos,$ypos+1];
        }

        if(isset($boardToSolve[$xpos-1][$ypos+1])){
            $safeMoves[] = [$xpos-1,$ypos+1];
        }

        return $safeMoves;


    }

    function solveBoggle($boardToSolve,$xpos,$ypos,$chain,$solutions) {
        //solveBoggle is all about recursion and working every leg of the board. I think it qualifies as a backtracking algorithm.
        //
        $chain[]= [$xpos,$ypos];

        sort($chain);

        if (count($chain) > 1 && sumGivenPostions($boardToSolve,$chain) == 9){
            if(!in_array($chain, $solutions)){
                $solutions[] = $chain;
            }
        }

        foreach (safeBoggleMoves($boardToSolve,$xpos,$ypos) as $move) {
            if (!in_array($move, $chain)){
                $solutions += solveBoggle($boardToSolve,$move[0],$move[1],$chain,$solutions);
            }
        }

        return $solutions;

    }
    //build random board
    $randboard = newBoggleBoard(3);
    $solutions = callSolveBoggle($randboard);
?>
<H1>The Board</H1>

<table>
    <?php for ($i=0; $i < 3; $i++) {
        echo "<tr>";
            for ($j=0; $j < 3; $j++) {
                echo "<td>";
                echo $randboard[$i][$j];
                echo"</td>";
            }
        echo "</tr>";
    }
    ?>
</table>
<h1> Answer Chains </h1>
<table>
    <?php foreach ($solutions as $solutions => $chain) {
        echo "<tr>";
        foreach ($chain as $chain => $tile) {
            echo "<td>";
            echo $randboard[$tile[0]][$tile[1]];
            echo"<td>";
        }
        echo "</tr>";
    }?>
</table>
</body>
</html>
