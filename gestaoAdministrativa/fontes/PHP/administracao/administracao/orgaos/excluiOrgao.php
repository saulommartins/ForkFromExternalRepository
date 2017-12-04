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
* Manutneção de orgãos
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18682 $
$Name$
$Author: cassiano $
$Date: 2006-12-11 15:37:41 -0200 (Seg, 11 Dez 2006) $

Casos de uso: uc-01.03.97
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include (CAM_FW_LEGADO."mascarasLegado.lib.php");
include (CAM_FW_LEGADO."funcoesLegado.lib.php");
include (CAM_FW_LEGADO."auditoriaLegada.class.php");
include (CAM_FW_LEGADO."configuracaoLegado.class.php");
include (CAM_FW_LEGADO."paginacaoLegada.class.php");

$stMascaraSetor = pegaConfiguracao("mascara_setor");
$arMascaraSetor = explode(".",$stMascaraSetor);
$stMascaraOrgao = $arMascaraSetor[0]."/9999";

$excluir = $_REQUEST['excluir'];
$controle = $_REQUEST['controle'];
$pagina = $_REQUEST['pagina'];

if (isset($excluir)) {
    $controle  = 1;
    $excluir   = explode("-",$excluir);
    $codOrgao  = $excluir[0];
    $pagina    = $excluir[1];
    $anoE      = $excluir[2];
}

if (!isset($controle)) {
    $controle = 0;
    $codOrgao = "";
    $nomOrgao = "";
    }

if (!isset($pagina)) {
   $pagina = 0;
}

?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($controle) {
case 0:
        $sSQLs = "
            SELECT
                cod_orgao, nom_orgao, ano_exercicio
            FROM
                administracao.orgao
            WHERE
                ano_exercicio = '".Sessao::getExercicio()."'
                AND cod_orgao > 0";
        Sessao::write('sSQLs',$sSQLs);
    /// Inicia o relatório em html
        $paginacao = new paginacaoLegada;
        $paginacao->pegaDados(Sessao::read('sSQLs'),"10");
        $paginacao->pegaPagina($pagina);
        $paginacao->geraLinks();
        $paginacao->pegaOrder("lower(nom_orgao)","ASC");
        $sSQL = $paginacao->geraSQL();
        //Pega os dados encontrados em uma query
        $conn = new dataBaseLegado;
        $conn->abreBD();
        $conn->abreSelecao($sSQL);
        $conn->fechaBD();
        $conn->vaiPrimeiro();
        if ($conn->numeroDeLinhas==0 && $pagina != 0) {
            mudaTelaPrincipal($PHP_SELF);
        }
        if ($conn->numeroDeLinhas==0) {
           exit("<br><b>Nenhum registro encontrado!</b>");
        }
        $html  = "";
        $html .= "
                  <table width='100%'>
                  <tr>
                      <td class='alt_dados' colspan='6'>Registros de Órgão</td>
                  </tr>
                  <tr>
                      <td class='labelleft' width='5%'>&nbsp;</td>
                      <td class='labelleft' width='10%'>Código</td>
                      <td class='labelleft' width='84%'>Órgão</td>
                      <td class='labelleft' width='1%'>&nbsp;</td>
                  </tr>";
        $count = $paginacao->contador();
        while (!$conn->eof()) {
                $codOrgao  = trim($conn->pegaCampo("cod_orgao"));
                $nomOrgao  = trim($conn->pegaCampo("nom_orgao"));
                $anoE  = trim($conn->pegaCampo("ano_exercicio"));
                $conn->vaiProximo();
                $stCodOrgao = $codOrgao."/".$anoE;
                $arCodOrgao = validaMascara($stMascaraOrgao,$stCodOrgao);
                if ($arCodOrgao[0]) {
                    $stCodOrgao = $arCodOrgao[1];
                }
                $stNomeOrgao = AddSlashes($stCodOrgao." - ".$nomOrgao);
                $html .= "
                <tr>
                       <td class='labelcenter'>".$count++."</td>
                       <td class=show_dados>".$stCodOrgao."</td>
                       <td class=show_dados>".$nomOrgao."</td>
                       <td class='botao'>
                       <a href='#' onClick=\"alertaQuestao('".$_SERVER['PHP_SELF']."?".Sessao::getId()."','excluir','".$codOrgao.'-'.$pagina.'-'.$anoE."','".$stNomeOrgao."','sn_excluir','".Sessao::getId()."');\">
                       <img src='".CAM_FW_IMAGENS."btnexcluir.gif' border=0></a></td>
                </tr>\n";
        }
        $html .= "</table>";
        echo $html;
        ?>
          <table width='450' align='center'><tr><td align='center'><font size='2'>
          <?php $paginacao->mostraLinks();  ?>
          </font></tr></td></table>
        <?php
        break;

case 1:
        $orgao = new configuracaoLegado();
        $orgao->setaValorOrgao($codOrgao,"",$anoE);
        $nomOrgao = pegaDado("nom_orgao","administracao.orgao","where cod_orgao = '".$codOrgao."' and ano_exercicio = '".$anoE."'");
        $stCodOrgao = $codOrgao."/".$anoE;
        $arCodOrgao = validaMascara($stMascaraOrgao,$stCodOrgao);
        if ($arCodOrgao[0]) {
            $stCodOrgao = $arCodOrgao[1];
        }
        $objeto = $stCodOrgao." - ".$nomOrgao;
        $pag = $PHP_SELF."?".Sessao::getId()."&pagina=".$pagina;
        if ($orgao->deleteOrgao()) {
             //Insere auditoria
             $audicao = new auditoriaLegada;
             $audicao->setaAuditoria(Sessao::read('numCgm'), Sessao::read('acao'), $objeto);
             $audicao->insereAuditoria();
             //Exibe mensagem e retorna para a página padrão
             alertaAviso($pag,$objeto,"excluir","aviso");
   } else {
        if ( strpos($orgao->stErro, "fk_") ) {
            $objeto = "O Órgão $objeto não pode ser excluído porque está sendo utilizado";
        }
        alertaAviso($pag,$objeto,"n_excluir","erro");
    }
    break;
}//Fim switch

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
