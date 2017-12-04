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
    * Arquivo que faz a exclusão dos bens de uma apólice
    * Data de Criação   : 26/03/2003

    * @author Desenvolvedor Leonardo Tremper

    * @ignore

    $Revision: 13075 $
    $Name$
    $Autor: $
    $Date: 2006-07-21 08:36:18 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.19  2006/07/21 11:35:23  fernando
Inclusão do  Ajuda.

Revision 1.18  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.17  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
setAjuda("UC-03.01.08");
if (isset($chave)) {
    $ctrl = 3;
}

if (!(isset($ctrl)))
    $ctrl = 0;

?>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/ifuncoesJs.js" type="text/javascript"></script>
<script src="../../../../../../gestaoAdministrativa/fontes/javaScript/funcoesJs.js" type="text/javascript"></script>

<?php

switch ($ctrl) {
      case 0:
?>
            <script type="text/javascript">
                  function Valida()
                  {
                        var mensagem = "";
                        var erro = false;
                        var campo;
                        var campo2;
                        var campoaux;
                        campo = document.frm.numApolice.value.length;
                        campo2 = document.frm.numCgm.value;
                        if ((campo == 0) && (campo2 == "xxx")) {
                              mensagem += "@Informe o Número da Apólice ou a Seguradora.";
                              erro = true;
                        }
                        if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                              return !(erro);
                  }
                  function Salvar()
                  {
                        if (Valida()) {
                              document.frm.submit();
                        }
                  }
            </script>
            <form name="frm" action="excluiBens.php?<?=Sessao::getId()?>" method="POST">
                  <input type="hidden" name="ctrl" value=1>
                  <table width="100%">
                        <tr>
                              <td class="alt_dados" colspan="2">Procure pelo Número da Apólice ou pela Seguradora</td>
                        </tr>
                        <tr>
                              <td class="label" width="20%" title="Informe o número da apólice.">Número da Apólice</td>
                        <td class="field"><input type="text" name="numApolice" size="14" maxlength="14" onKeyPress="return(isValido(this,event,'0123456789abcdefghijklmnopqrstuvxywzABCDEFGHIJKLMNOPQRSTUVXYWZ'));"></td>
                        </tr>
                        <tr>
                              <td class="label" title="Informe o nome da seguradora." >Seguradora</td>
                              <td class="field">
<?php
      include_once '../apolice.class.php';
      $bemsegurado = new apolice;
      $bemsegurado->listaComboSeguradoras();
      $bemsegurado->mostraComboSeguradoras();
?>
                              </td>
                        </tr>
                        <tr>
                              <td colspan='2' class='field'><?php geraBotaoOk(); ?></td>
                        </tr>
                  </table>
            </form>
<?php
      break;
// busca e exibe BENS encontrados de acordo com os dados/critérios informados
      case 1:
// pesquisa por APOLICE
            if (($numApolice != "") AND ($numCgm == "xxx"))
                  $ApCgWhere = "lower(a.num_apolice) LIKE lower('%".$numApolice."%')";
// pesquisa por SEGURADORA
            if (($numApolice == "") AND ($numCgm != "xxx"))
                  $ApCgWhere = "a.numcgm = ".$numCgm;
// pesquisa por APOLICE e SEGURADORA
            if (($numApolice != "") AND ($numCgm != "xxx")) {
                  $ApCgWhere = "lower(a.num_apolice) LIKE lower('%".$numApolice."%')";
                  $ApCgWhere .= " AND a.numcgm = ".$numCgm;
            }
?>
                  <table width="100%">
<?php
      $sSQL = "
            SELECT
                  a.cod_apolice,
                  a.numcgm,
                  a.num_apolice,
                  a.dt_vencimento,
                  a.contato,
                  cgm.nom_cgm
            FROM
                  patrimonio.apolice as a,
                  sw_cgm as cgm
            WHERE
                  a.numcgm = cgm.numcgm
                  AND ".$ApCgWhere;
      $sessao->transf = $sSQL;
// gera lista de atributos com paginacao
      include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
      $paginacao = new paginacaoLegada;
      $paginacao->pegaDados($sessao->transf,"10");
      $paginacao->pegaPagina($pagina);
      $paginacao->geraLinks();
      $paginacao->pegaOrder("num_apolice","ASC");
      $sSQL = $paginacao->geraSQL();
//print $sSQL;
      $dbEmp = new dataBaseLegado;
      $dbEmp->abreBD();
      $dbEmp->abreSelecao($sSQL);
      $dbEmp->vaiPrimeiro();
?>
                        <tr>
                              <td class="alt_dados" colspan="5">Registros de Bens Segurados</td>
                        </tr>
                        <tr>
                              <td class="labelcenter" width="5%">&nbsp;</td>
                              <td class="labelcenter" width="15%">Número da Apólice</td>
                              <td class="labelcenter" width="60%">Seguradora</td>
                              <td class="labelcenter" width="16%">Data de Vencimento</td>
                              <td class="labelcenter" width="4%">&nbsp;</td>
                        </tr>
<?php
      $cont = $paginacao->contador();
      while (!$dbEmp->eof()) {
            $codApolice     = trim($dbEmp->pegaCampo("cod_apolice"));
            $numcgm         = trim($dbEmp->pegaCampo("numcgm"));
            $numApolice     = trim($dbEmp->pegaCampo("num_apolice"));
            $dt_vencimento  = dataToBr(trim($dbEmp->pegaCampo("dt_vencimento")));
            $contato        = trim($dbEmp->pegaCampo("contato"));
            $nomCgm         = trim($dbEmp->pegaCampo("nom_cgm"));
            $dbEmp->vaiProximo();
?>
                        <tr>
                              <td class="labelcenter"><?=$cont++;?></td>
                              <td class="show_dados_right"><?=$numApolice;?></td>
                              <td class="show_dados"><?=$nomCgm;?></td>
                              <td class="show_dados_center"><?=$dt_vencimento;?></td>
                              <td class="botao" width=5>
                                    <a href='#' onClick="mudaTelaPrincipal('excluiBens.php?<?=Sessao::getId();?>&codApolice=<?=$codApolice;?>&ctrl=2');">
                                          <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btneditar.gif' title="Alterar" border='0'>
                                    </a>
                              </td>
                        </tr>
<?php
      }
?>
                  </table>
<?php
      $dbEmp->limpaSelecao();
      $dbEmp->fechaBD();
      echo "<table width=100% align=center><tr><td align=center><font size=2>";
      $paginacao->mostraLinks();
      echo "</font></tr></td></table>";
      break;

      case 2:
            $sSQL = "
            SELECT
                  num_apolice
            FROM
                  patrimonio.apolice
            WHERE
                  cod_apolice = ".$codApolice;
      $dbEmp = new dataBaseLegado;
      $dbEmp->abreBD();
      $dbEmp->abreSelecao($sSQL);
      $dbEmp->vaiPrimeiro();
      $numApolice  = trim($dbEmp->pegaCampo("num_apolice"));
      $dbEmp->limpaSelecao();
      $dbEmp->fechaBD();
?>
                  <table width="100%">
                        <tr>
                              <td class="alt_dados" colspan="5">Bens da apólice n.:<?=$numApolice;?></td>
                        </tr>
<?php
//*******************************************************
//Mostra  os Bens dsta apólice
//*******************************************************
      $sSQL = "
            SELECT DISTINCT
                  ab.cod_bem, e.nom_especie, b.descricao
            FROM
                  patrimonio.bem as bae,
                  patrimonio.especie as e,
                  patrimonio.apolice_bem as ab,
                  patrimonio.apolice as a,
                  patrimonio.vw_bem_ativo as b
            WHERE
                  ab.cod_apolice = ".$codApolice."
                  AND ab.cod_bem = bae.cod_bem
                      AND bae.cod_especie = e.cod_especie
                  AND bae.cod_grupo = e.cod_grupo
                  AND bae.cod_natureza = e.cod_natureza
                  AND ab.cod_apolice = a.cod_apolice
                  AND ab.cod_bem = b.cod_bem";
      $dbEmp = new dataBaseLegado;
      $dbEmp->abreBD();
      $dbEmp->abreSelecao($sSQL);
      $dbEmp->vaiPrimeiro();
?>
                        <tr>
                              <td class="labelcenter" width="5%">&nbsp;</td>
                              <td class="labelcenter" width="10%">Código</td>
                              <td class="labelcenter" width="30%">Espécie</td>
                              <td class="labelcenter" width="55%">Descrição</td>
                              <td class="labelcenter" width="5%">&nbsp;</td>
                        </tr>
<?php
      if ($dbEmp->numeroDeLinhas == 0) {
            echo "<tr><td class=show_dados colspan=5>Ainda não há bens cadastrados nesta Apólice</td>\n";
      } else {
            $cont = 1;
            while (!$dbEmp->eof()) {
                  $codBem  = trim($dbEmp->pegaCampo("cod_bem"));
                  $nomEspecie  = trim($dbEmp->pegaCampo("nom_especie"));
                  $descricao  = trim($dbEmp->pegaCampo("descricao"));
                  $chave = $codBem."-".$codApolice;
                  $dbEmp->vaiProximo();
?>
                        <tr>
                              <td class="labelcenter"><?=$cont++;?></td>
                              <td class="show_dados_right"><?=$codBem;?></td>
                              <td class="show_dados"><?=$nomEspecie;?></td>
                              <td class="show_dados"><?=$descricao;?></td>
                              <td class="botao" width=5>
                                    <a href='' onClick="alertaQuestao('../../../../../../gestaoPatrimonial/fontes/PHP/patrimonio/patrimonio/bensSegurados/excluiBens.php?<?=Sessao::getId();?>','chave','<?=$chave;?>','Bem: <?=$descricao;?>','sn_excluir','<?=Sessao::getId();?>');">
                                          <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif' title="Excluir" border='0'>
                                    </a>
                              </td>
                        </tr>
<?php
            }
      }
      $dbEmp->limpaSelecao();
      $dbEmp->fechaBD();
?>
                  </table>
<?php
      break;
// executa operacao de exclusao do BEM no BD
      case 3:
            $variaveis = explode("-",$chave);
            $codBem = $variaveis[0];
            $codApolice = $variaveis[1];
            include_once '../apolice.class.php';
            $apoliceBem = new apolice;
            $apoliceBem->setaVariaveisBemApolice($codApolice, $codBem);
            if ($apoliceBem->deleteBemApolice()) {
                  include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                  $audicao = new auditoriaLegada;
                  $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codApolice);
                  $audicao->insereAuditoria();
                  echo '
                  <script type="text/javascript">
                        alertaAviso("Bem '.$codBem.' excluído da Apólice '.$codApolice.'","unica","aviso","'.Sessao::getId().'");
                        mudaTelaPrincipal("excluiBens.php?'.Sessao::getId().'&ctrl=2&codApolice='.$codApolice.'");
                  </script>
                  ';
            } else {
                  echo '
                  <script type="text/javascript">
                        alertaAviso("Não foi possível excluir o bem '.$codBem.'da Aopólice '.$codApolice.'","unica","aviso","'.Sessao::getId().'");
                        mudaTelaPrincipal("excluiBens.php?'.Sessao::getId().'&ctrl=2&codApolice='.$codApolice.'");
                  </script>
                  ';
            }
      break;
}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
