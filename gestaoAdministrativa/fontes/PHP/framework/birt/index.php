<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<head>
    <title>Frameset - Relatório</title>
    <link href="main.css" rel="stylesheet" type="text/css" />
</head>
<body>
    <div id="conteudo">
        <div id="incTopo">
            <ul id="menu">
                <li><img src="" alt="HTML" /></li>
                <li><img src="" alt="PDF" /></li>
                <li><img src="" alt="Impress&atilde;o" /></li>
                <li><img src="" alt="Configura&ccedil;&atilde;o" /></li>
            </ul>
            <dl>
                <dt>Nome do Relatório Gerado no BIRT</dt>
                <dd class="defPrefeitura">Prefeitura Municipal de Eldorado do Sul</dd>
                <dd class="defExercicio">Exercicio: <?php echo date("Y"); ?></dd>
                <dd class="defData">Data: <?php echo date("d/m/y - H:i");?></dd>
            </dl>
        </div>
        <div id="corpo">conte&uacute;do</div>
    </div>
</body>
</html>
