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
<?php
/**
* Arquivo de implementação de manutenção de processo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28826 $
$Name$
$Author: rodrigosoares $
$Date: 2008-03-27 16:33:30 -0300 (Qui, 27 Mar 2008) $

Casos de uso: uc-01.06.98
*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include("../../../framework/include/cabecalho.inc.php");
?>
<script type="text/javascript">
    function voltaProcesso()
    {
        document.frm.action = "consultaProcesso.php?<?=Sessao::getId()?>";
        document.frm.submit();
    }
</script>
<?php

    echo "<form name=frm action=consultaProcesso.php?".Sessao::getId()." method=post>";
    echo "<table width=70%>";
    echo "<input type='hidden' name='codProcesso' value='".$codProcesso."'>\n";
    echo "<input type='hidden' name='controle' value='0'>\n";
    echo "<input type='hidden' name='ctrl' value='2'>\n";
    echo "<input type=hidden name=anoExercicio value=".$anoExercicio.">\n";
    echo "<input type=hidden name=pagina value=".$pagina.">\n";
    echo "<input type=hidden name=verificador value='true'>\n";
    //echo "<input type=hidden name=pagina value=".$pagina.">";

        $select = 	"SELECT
                    dp.cod_documento,
                    d.nom_documento
                    FROM
                    sw_documento_processo as dp,
                    sw_documento as d
                    WHERE
                    dp.cod_processo = ".$codProcesso." AND
                    dp.cod_documento = d.cod_documento";
        //echo $select."<br>\n";
        $dbConfig = new database;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);
        echo "<tr><td class=alt_dados colspan=2>Documentos Entregues</td></tr>";
        $nomAnt = "";
        while (!$dbConfig->eof()) {
            $codDoc = $dbConfig->pegaCampo("cod_documento");
            $nomDoc = $dbConfig->pegaCampo("nom_documento");
            if ($nomAnt != $nomDoc) {
                echo "<tr><td class=label>".$nomDoc."</td><td class=show_dados>\n";
            }
            $selectCopia = 	"SELECT
                            imagem,
                            anexo
                            FROM
                            sw_copia_digital
                            WHERE
                            cod_processo = '".$codProcesso."' AND
                            cod_documento = ".$codDoc;
            $dbCopia = new database;
            $dbCopia->abreBd();
            $dbCopia->abreSelecao($selectCopia);
            while (!($dbCopia->eof())) {
                $tipoAnexo = $dbCopia->pegaCampo("imagem");
                $anexo = $dbCopia->pegaCampo("anexo");
                if ($tipoAnexo == "t") {
                    $anexoImg = pathinfo($anexo);
                    echo "<a href=../../anexos/".$anexoImg["basename"]." target='_blank'><img src='../../images/imagem.png'></a>&nbsp;\n";
                } elseif ($tipoAnexo == "f") {
                    //$anexoDoc = explode("/", $anexo);
                    $anexoDoc = pathinfo($anexo);
                    echo "<a href=../../anexos/".$anexoDoc["basename"]." target='_blank'><img src='../../images/outDoc.png'></a>&nbsp;\n";
                }
                $dbCopia->vaiProximo();
            }
            //echo "</td></tr>";
            $dbConfig->vaiProximo();
            $nomAnt = $nomDoc;
        }
        if (!(isset($anexo))) {
            echo "<tr><td class=show_dados colspan=2>Nenhum Documento Entregue</td></tr>\n";
        }
        $dbConfig->limpaSelecao();
        $dbConfig->fechaBd();
?>
    <tr><td class=fieldcenter colspan=2><input type="button" name="voltar" value="Voltar para Processo" onclick="voltaProcesso();"></td></tr>
</table>
</form>

<?php
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
//	include("../../includes/rodape.php");
?>
