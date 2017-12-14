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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Emissão de Carnê de IPTU</title>

    <link rel="stylesheet" href="../../../../../../web/css/tela.css" type="text/css" media="screen" />

</head>
<body>
<div id="page">

<h1>IPTU<span>2a Via do Carnê</span></h1>
<div id="conteudo">
    <form id="frm" action="ListarParcelas.php" method="post">
        <div class="campo_bloco">
            <label for="im">Matrícula do Imóvel</label>
            <input type="text" name="im" id="im" size="10" />
        </div>
        <br />
        <div class="campo_bloco">
            <label for="exercicio">Exercício</label>
            <input type="text" name="exercicio" id="exercicio" size="10" maxlength="4" />
        </div>
        <br />
        <div class="campo_bloco">
            <input type="submit" id="enviar" name="enviar" value="Consultar" />
        </div>
    </form>
</div>

<div>
</body>
</html>
