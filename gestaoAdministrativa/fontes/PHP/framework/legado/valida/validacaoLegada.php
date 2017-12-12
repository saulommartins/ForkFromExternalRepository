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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

//include "cabecalho.php";
include_once '../../../framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';

$js = "";
$ok = true;

switch ($ctrl) {
case ("cgm"):
    if ($numCgm>0) {
        if ($nomCgm = pegaDado("nom_cgm","sw_cgm","WHERE numcgm = '".$numCgm."' ")) {
            $js .= "f.".$campo.".value = '".$nomCgm."'; \n";
        } else {
            $js .= "f.".$campo.".value = ''; \n";
            $js .= "alertaAviso('CGM Não Existe','','','Sessao::getId().');";
        }
    } else {
        $js .= "f.".$campo.".value = ''; \n";
    }
    break;
case ("motorista"):
    if ($numCgm>0) {
        $sWhere = " WHERE numcgm = '".$numCgm."'
                    AND numcgm IN (Select numcgm From sw_cgm)";
        if ($nomCgm = pegaDado("nom_cgm","sw_cgm",$sWhere)) {
            $js .= "f.".$campo.".value = '".$nomCgm."'; \n";
        } else {
            $js .= "f.".$campo.".value = 'Motorista Não Existe'; \n";
        }
    } else {
        $js .= "f.".$campo.".value = 'Motorista Não Existe'; \n";
    }
    break;
//Verifica se o setor digitado é válido. Caso seja retorna o nome do setor
case ("setor"):
    $setor = validaSetor($chave,$exercicio);
    if ($setor) {
        $js .= "f.".$campo.".value = '".$setor[nomSetor]."'; \n";
    } else {
        $js .= "f.".$campo.".value = 'Setor Não Existe'; \n";
    }
    break;
}//Fim switch($ctrl)

?>
<html>
<head>
<script type="text/javascript">
function executa()
{
    var mensagem = "";
    var erro = false;
    var f = window.parent.frames["telaPrincipal"].document.frm;
    var d = window.parent.frames["telaPrincipal"].document;
    var aux;
    <?php echo $js; ?>

    if (erro) SistemaLegado::alertaAviso(mensagem,'form','erro');
}
</script>

</head>

<body onLoad="javascript:executa();">

</body>

</html>
