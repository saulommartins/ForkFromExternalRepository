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
    * Arquivo que faz a inserção dos bens em uma apólice
    * Data de Criação   : 26/03/2003

    * @author Desenvolvedor Leonardo Tremper
    * @author Desenvolvedor Marcelo B. Paulino

    * @ignore

    $Revision: 17896 $
    $Name$
    $Autor: $
    $Date: 2006-11-20 14:02:44 -0200 (Seg, 20 Nov 2006) $

    * Casos de uso: uc-03.01.08
*/

/*
$Log$
Revision 1.40  2006/11/20 16:02:44  bruce
Bug #6932#

Revision 1.39  2006/07/21 11:35:23  fernando
Inclusão do  Ajuda.

Revision 1.38  2006/07/13 19:57:56  fernando
Alteração de hints e target do form

Revision 1.37  2006/07/11 17:07:01  fernando
Retirado o rótulo do cabeçalho Ação da lista.

Revision 1.36  2006/07/06 14:06:58  diego
Retirada tag de log com erro.

Revision 1.35  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/dataBaseLegado.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/funcoesLegado.lib.php';
include_once (CAM_GP."javaScript/ifuncoesJsGP.js");
setAjuda("UC-03.01.08");
if (!(isset($ctrl))) {
    $ctrl = 0;
}

switch ($ctrl) {

    case 0:
        $sessao->transf = '';
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

      <form name="frm" action="incluiBens.php?<?=Sessao::getId()?>" method="POST">
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
                        <td class="label" title="Informe o nome da seguradora.">Seguradora</td>
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
            if (($numApolice != "") AND ($numCgm == "xxx")) {
                  $ApCgWhere = "lower(a.num_apolice) LIKE lower('%".$numApolice."%')";
            }

            // pesquisa por SEGURADORA
            if (($numApolice == "") AND ($numCgm != "xxx")) {
                  $ApCgWhere = "a.numcgm = ".$numCgm;
            }

            // pesquisa por APOLICE e SEGURADORA
            if (($numApolice != "") AND ($numCgm != "xxx")) {
                  $ApCgWhere = "lower(a.num_apolice) LIKE lower('%".$numApolice."%')";
                  $ApCgWhere .= " AND a.numcgm = ".$numCgm;
            }
?>
            <table width="100%">
<?php
            if ($sessao->transf) {
               $sSQL = $sessao->transf;
            } else {
               $sSQL = "SELECT
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
            }
// gera lista de atributos com paginacao
            include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/paginacaoLegada.class.php';
            $paginacao = new paginacaoLegada;
            $paginacao->pegaDados($sessao->transf,"10");
            $paginacao->pegaPagina($pagina);
            $paginacao->geraLinks();
            $paginacao->pegaOrder("num_apolice","ASC");
            $sSQL = $paginacao->geraSQL();

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
                        <a href='#' onClick="mudaTelaPrincipal('incluiBens.php?<?=Sessao::getId();?>&codApolice=<?=$codApolice;?>&ctrl=2');">
                              <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/botao_encaminhar.png' title="Incluir"  border='0'>
                        </a>
                  </td>
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
                  num_apolice, nom_cgm
            FROM
                  patrimonio.apolice, sw_cgm
            WHERE sw_cgm.numcgm = apolice.numcgm
              AND cod_apolice = ".$codApolice;
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $numApolice  = trim($dbEmp->pegaCampo("num_apolice"));
            $nomCgm = trim($dbEmp->pegaCampo("nom_cgm"));
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();

            $sSQL = "
            SELECT DISTINCT
                  ab.cod_bem,
                  e.nom_especie,
                  b.descricao
            FROM
                  patrimonio.bem as bae,
                  patrimonio.especie as e,
                  patrimonio.apolice_bem as ab,
                  patrimonio.apolice as a,
                  patrimonio.vw_bem_ativo as b
            WHERE
                  ab.cod_apolice = ".$codApolice." AND
                  ab.cod_bem = bae.cod_bem AND
                  bae.cod_especie = e.cod_especie AND
                  bae.cod_grupo = e.cod_grupo AND
                  bae.cod_natureza = e.cod_natureza AND
                  ab.cod_apolice = a.cod_apolice AND
                  ab.cod_bem = b.cod_bem";

            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
            $sessao->arBens = array();
            while (!$dbEmp->eof()) {
               $arElementos = array();
               $arElementos['codBem'] = $dbEmp->pegaCampo('cod_bem');
               $arElementos['nomEspecie'] = $dbEmp->pegaCampo('nom_especie');
               $arElementos['descricao'] = $dbEmp->pegaCampo('descricao');
               $arElementos['descricao'] = str_replace('\"', '"', str_replace(chr(13).chr(10)," ",$arElementos['descricao']));
               $arElementos['descricao'] = str_replace('\\\'', '\'', str_replace(chr(13).chr(10)," ",$arElementos['descricao']));

               $sessao->arBens[] = $arElementos;
               $dbEmp->vaiProximo();
            }
?>
      <script type="text/javascript">
            function Valida()
            {
                  var mensagem = "";
                  var erro = false;
                  var campo;
                  var campoaux;
                  campo = document.frm.codBem.value.length;
                  if (campo == 0) {
                        mensagem += "@O campo Código do Bem é obrigatório.";
                        erro = true;
                  }
                  campo = document.frm.codBem.value;
                  if (isNaN(campo)) {
                        mensagem += "@O campo Código do Bem só aceita números.";
                        erro = true;
                  }
                  if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId()?>');
                        return !(erro);
            }
            function Salvar()
            {
                        document.frm.ctrl.value     = 3 ;
                        document.frm.submit();
            }
            function Incluir()
            {
                 if (Valida()) {
                      var stTarget;
                      stTarget = document.frm.target;
                      document.frm.target = 'oculto';
                      document.frm.ctrl.value     = 7 ;
                      document.frm.submit();
                      document.frm.target = stTarget;
                      document.frm.codBem.value = '';
                      document.getElementById('lblDescricaoBem').innerHTML='&nbsp;';
                      document.frm.numPlaca.value = '';
                 }
            }
            function Limpar()
            {
                document.getElementById('lblDescricaoBem').innerHTML='&nbsp;';
                parent.window.frames['telaPrincipal'].document.all.codBem.value = '';
                parent.window.frames['telaPrincipal'].document.all.numPlaca.value = '';
                parent.window.frames['telaPrincipal'].document.all.codBem.disabled = false;
                parent.window.frames['telaPrincipal'].document.all.numPlaca.disabled = false;
                parent.window.frames['telaPrincipal'].document.getElementById('lblDescricaoBem').innerHTML='&nbsp;';
                parent.window.frames['telaPrincipal'].document.all.codBem.focus();

            }
            function Cancela()
            {
                      var stTarget;
                      stTarget = document.frm.target;
                      document.frm.target = 'telaPrincipal';
                      document.frm.ctrl.value     = 1 ;
                      document.frm.submit();
            }

            function Finalizar(sAp)
            {
                  sPag = "incluiBens.php?<?=Sessao::getId()?>&ctrl=4&codApolice="+sAp;
                  parent.frames["telaPrincipal"].location.replace(sPag);
            }

            function procuraBemPlaca(T)
            {
                if (document.frm.codBem.value == '') {

                    var stTarget;
                    stTarget = document.frm.target;
                    document.frm.target         = "oculto";
                    document.frm.ctrl.value     = 4 ;
                    document.frm.submit();
                    document.frm.target = stTarget;

//                    document.frm.codBem.readOnly = true;
//                    document.frm.numPlaca.readOnly = false;
                }
//                if (document.frm.codBem.value != '') {
//                    document.frm.codBem.readOnly = false;
//                }
            }

        function excluiDado(inCodBem)
        {
            var stTarget;
            stTarget = document.frm.target;
            document.frm.inCodBemDeletar.value = inCodBem;
            document.frm.target = 'oculto';
            document.frm.ctrl.value = 6;
            document.frm.submit();
            document.frm.target = stTarget;

        }
        function verificaCampo(objOrigem,objDestino,prm)
        {
//          var codBem = objOrigem;
          var objOrigem  = eval("document.frm."+objOrigem);
          var objDestino = eval("document.frm."+objDestino);
//          if (objOrigem.value != "" & objOrigem.readOnly != true) {
            if (objOrigem.value != '') {
                objDestino.disabled = true;
                document.forms[0].target     = "oculto";
                document.forms[0].ctrl.value = prm ;
                document.forms[0].submit();
            } else {
                objDestino.value = '';
                objDestino.disabled = false;
                objOrigem.disabled = false;
                document.getElementById('lblDescricaoBem').innerHTML='&nbsp;';
            }
//            objDestino.readOnly          = true;
//          } elseif (objOrigem.value == "" & objDestino.value != "") {
//            if (codBem == 'codBem') {
//                objOrigem.readOnly = true;
  //              parent.window.frames['telaPrincipal'].document.getElementById('lblDescricaoBem').innerHTML='&nbsp;';
//                objOrigem.value = '';
  //              objDestino.value = '';
//            }
//            objDestino.readOnly = false;
//          } else {
//            if (codBem == 'codBem') {
//               objOrigem.readOnly = true;
//               parent.window.frames['telaPrincipal'].document.getElementById('lblDescricaoBem').innerHTML='&nbsp;';
//              objOrigem.value = '';
//                objDestino.value = '';
//            }
//            objDestino.readOnly = false;
//          }
//          document.forms[0].target = "telaPrincipal";
        }

      </script>
      <form name="frm" action="incluiBens.php?<?=Sessao::getId()?>" method="post">
            <input type="hidden" name="codApolice" value="<?=$codApolice;?>">
            <input type="hidden" name="numApolice" value="<?=$numApolice;?>">
            <input type="hidden" name="ctrl" value="3">
            <input type="hidden" name="inCodBemDeletar">
            <table width="100%">
                  <tr>
                        <td class="label" width="20%" title="">Apólice</td>
                        <td class="field"><?=$numApolice;?></td>
                  </tr>
                  <tr>
                        <td class="label" width="20%" title="">Seguradora</td>
                        <td class="field"><?=$nomCgm;?></td>
                  </tr>

                  <tr>
                     <td class="label" width="20%" title="Informe o código do bem.">*Código do Bem</td>
                     <td class="field">
                       <table width="100%" border="0" cellpadding=0 cellspacing=0>
                         <tr>
                           <td>

                            <input type="text" name="codBem" size="10" maxlength="10" onBlur="verificaCampo(this.name,'numPlaca','5')" onKeyPress="return(isValido(this,event,'0123456789'));"   >&nbsp;
                           </td>
                           <td align="left" width="64%" id="lblDescricaoBem" name="sFornecedor" class="fakefield" valign="middle">&nbsp;</td>
                           <td width="23%">&nbsp;
                             <a href="javascript:procuraBemGP('frm','codBem','<?=Sessao::getId()?>');">
                               <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar bem" border="0" align="absmiddle">
                             </a>
                           </td>
                         </tr>
                      </table>
                     </td>
                  </tr>

                  <tr>
                        <td class="label" width="20%" title="Informe a placa de identificação do bem.">Placa de Identificação</td>
                        <td class="field">
                              <input type="text" name="numPlaca" size="10" maxlength="10"  onBlur="verificaCampo(this.name,'codBem','4')"  >&nbsp;
                                    <a href="javascript:procuraBemGP('frm','codBem','<?=Sessao::getId()?>');">
                                    <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/procuracgm.gif" title="Buscar placa" border="0" align="absmiddle"></a>
                        </td>
                  </tr>

                  <tr>
                        <td colspan='2' class='field'><?php
    $html = '<table width="100%" cellspacing=0 border=0 cellpadding=0><tr><td>';
    $html .= '<input type="button" name="incluir" value="Incluir" style="width: 60px" onClick="Incluir();">';
    $html .= '&nbsp;<input type="reset" name="limpar" value="Limpar" style="width: 60px" onClick="Limpar();">';
    $html .= '</td><td class="fieldright_noborder">';
    $html .= '<b>* Campos obrigatórios</b>';
    $html .= '</td></tr></table>';
    print $html;

                        ?></td>
                  </tr>
            </table>
      </form>
      <span id='spnLista'>
<!-- LISTA OS BENS RELACIONADOS A APOLICE SELECIONADA -->
            <table width="100%">
            <tr>
                  <td class="alt_dados" colspan="5">Bens da Apólice</td>
            </tr>
<?php
            $arBens = $sessao->arBens;
?>
            <tr>
                <td class="labelcenter" width="5%">&nbsp;</td>
                <td class="labelcenter" width="10%">Código</td>
                <td class="labelcenter" width="30%">Espécie</td>
                <td class="labelcenter" width="55%">Descrição</td>
                <td class="labelcenter" width="3%">&nbsp;</td>
            </tr>
<?php
            if (empty($arBens)) {
?>
                  <tr>
                        <td class="show_dados" colspan="5">Ainda não há bens cadastrados nesta Apólice</td>
                  </tr>
<?php
            } else {
                  $cont = 1;
                  for ($i=0;$i<count($arBens);$i++) {
                        $codBem     = trim($arBens[$i]["codBem"]);
                        $nomEspecie = trim($arBens[$i]["nomEspecie"]);
                        $descricao  = trim($arBens[$i]["descricao"]);
                        $descricao = str_replace('\"', '"', str_replace(chr(13).chr(10)," ",$descricao));
                        $descricao = str_replace('\\\'', '\'', str_replace(chr(13).chr(10)," ",$descricao));

?>
                  <tr>
                        <td class="labelcenter"><?=$cont++;?></td>
                        <td class="show_dados_right"><?=$codBem;?></td>
                        <td class="show_dados"><?=$nomEspecie;?></td>
                        <td class="show_dados"><?=$descricao;?></td>
                        <td class=botao width=5>
                        <a href='JavaScript:excluiDado(<?=$codBem?>);'>
                        <img src='../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif' title='Excluir' border='0'></a>
                  </tr>
<?php
                }
            }
            $dbEmp->limpaSelecao();
            $dbEmp->fechaBD();
?>
            </table>
            </span>

            <table width="100%">
                  <tr>
                        <td colspan='5' class='field'><?php geraBotaoOk(1,0,1,1); ?></td>
                  </tr>

            </table>
<?php
    break;

    case 3:
        include_once '../apolice.class.php';

        $apoliceBem = new apolice;
        $apoliceBem->setaVariaveisBemApolice( $codApolice, null );
                if ( $apoliceBem->insereArrayBens($sessao->arBens) ) {
                    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/auditoriaLegada.class.php';
                    $audicao = new auditoriaLegada;
                    $audicao->setaAuditoria(Sessao::read('numCgm'), $sessao->acao, $codApolice);
                    $audicao->insereAuditoria();
?>
                    <script type="text/javascript">
                        alertaAviso("Apólice(<?=$numApolice;?>) alterada com sucesso.","alterar","aviso","<?=Sessao::getId();?>");
                        document.target = "telaPrincipal";
                        parent.window.frames['telaPrincipal'].location = "incluiBens.php?<?=Sessao::getId();?>&ctrl=1&codApolice=<?=$codApolice;?>";
                       /* window.location = "incluiBens.php?<?=Sessao::getId();?>&ctrl=1&codApolice=<?=$codApolice;?>";*/
                    </script>
<?php
                } else {
?>
                    <script type="text/javascript">
                        alertaAviso("Apólice: <?=$numApolice;?>","n_alterar","erro","<?=Sessao::getId();?>");
                    </script>
<?php
                }
    break;

    case 4:
            $placa = $_REQUEST['numPlaca'];
            $select = "select cod_bem
                              ,descricao
                       from patrimonio.bem
                       where patrimonio.bem.num_placa = '$placa' ";

            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($select);
            if ($dbEmp->eof()) {
               echo "<script>parent.window.frames['telaPrincipal'].document.all.numPlaca.disabled = false;</script>";
               echo "<script>parent.window.frames['telaPrincipal'].document.all.codBem.disabled = false;</script>";
               echo "<script>parent.window.frames['telaPrincipal'].document.all.numPlaca.value = '';</script>";
               echo "<script>parent.window.frames['telaPrincipal'].document.all.codBem.value = '';</script>";
               echo "<script>parent.window.frames['telaPrincipal'].document.all.numPlaca.focus() ;</script>";
               echo "<script>parent.window.frames['telaPrincipal'].document.getElementById('lblDescricaoBem').innerHTML='&nbsp;';</script>";
               sistemaLegado::exibeAviso("A placa digitada não existe!"," "," ");

            } else {
              echo "<script>parent.window.frames['telaPrincipal'].document.all.codBem.value = '".$dbEmp->pegaCampo('cod_bem') ."';</script>";
              $descricao = $dbEmp->pegaCampo('descricao');
              $descricao = str_replace('"', '\"', str_replace(chr(13).chr(10)," ",$descricao));
              $descricao = str_replace('\'', '\\\'', str_replace(chr(13).chr(10)," ",$descricao));
              echo "<script>parent.window.frames['telaPrincipal'].document.getElementById('lblDescricaoBem').innerHTML='".$descricao."';</script>";

            }
    break;

    case 5:
            $codBem = $_REQUEST['codBem'] ;
            $select = "select num_placa
                             ,descricao
                       from patrimonio.bem
                       where patrimonio.bem.cod_bem = $codBem ";
            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($select);
            if ($dbEmp->eof()) {
               $js =" parent.window.frames['telaPrincipal'].document.all.codBem.disabled = false;";
               $js.=" parent.window.frames['telaPrincipal'].document.all.numPlaca.disabled = false;";
               $js.=" parent.window.frames['telaPrincipal'].document.all.codBem.value = '';";
               $js.=" parent.window.frames['telaPrincipal'].document.all.codBem.focus();";
               $js.=" parent.window.frames['telaPrincipal'].document.all.numPlaca.value = '';";
               $js.=" if (parent.window.frames['telaPrincipal'].document.getElementById('lblDescricaoBem')) {";
               $js.="  parent.window.frames['telaPrincipal'].document.getElementById('lblDescricaoBem').innerHTML='&nbsp;';}";
               sistemaLegado::exibeAviso("O bem ".$codBem." não existe."," "," ");
            } else {
              //incluido para eliminar os erros com aspas simples (') , aspas duplas (") e quebra de linha no nome da descrição

              $descricao = $dbEmp->pegaCampo('descricao');
              $descricao = str_replace('"', '\"', str_replace(chr(13).chr(10)," ",$descricao));
              $descricao = str_replace('\'', '\\\'', str_replace(chr(13).chr(10)," ",$descricao));

              $js ="if (parent.window.frames['telaPrincipal'].document.getElementById('lblDescricaoBem')) {";
              $js.=" parent.window.frames['telaPrincipal'].document.getElementById('lblDescricaoBem').innerHTML='".$descricao."';}";
              $js.="parent.window.frames['telaPrincipal'].document.all.numPlaca.value = '".$dbEmp->pegaCampo('num_placa')."';";
            }
              echo "<script>".$js."</script>";
    break;

    case 6:
       $html = '<table width="100%">';
       $html .= '<tr>';
       $html .= '<td class="alt_dados" colspan="5">Bens da Apólice</td>';
       $html .= '</tr>';

       $html .= '<tr>';
       $html .= '    <td class="labelcenter" width="5%">&nbsp;</td>';
       $html .= '    <td class="labelcenter" width="10%">Código</td>';
       $html .= '    <td class="labelcenter" width="30%">Espécie</td>';
       $html .= '    <td class="labelcenter" width="55%">Descrição</td>';
       $html .= '    <td class="labelcenter" width="3%">&nbsp;</td>';
       $html .= '</tr>';

       $arBens = array();

       foreach ($sessao->arBens as $arBem) {
            if ($arBem['codBem'] != $inCodBemDeletar) {
               $arTemp['codBem'] = $arBem['codBem'];
               $arTemp['nomEspecie'] = $arBem['nomEspecie'];

               $arBem['descricao'] = str_replace('"', '\"', str_replace(chr(13).chr(10)," ",$arBem['descricao']));
               $arBem['descricao'] = str_replace('\'', '\\\'', str_replace(chr(13).chr(10)," ",$arBem['descricao']));
               $arTemp['descricao'] = $arBem['descricao'];

               $arBens[] = $arTemp;
            }
       }
       $sessao->arBens = $arBens;

       if (empty($arBens)) {
           $html.= '<tr>';
           $html.= '      <td class="show_dados" colspan="5">Ainda não há bens cadastrados nesta Apólice</td>';
           $html.= '</tr>';
       } else {
           $cont = 1;
           for ($i=0;$i<count($arBens);$i++) {
                 $codBem     = trim($arBens[$i]["codBem"]);
                 $nomEspecie = trim($arBens[$i]["nomEspecie"]);
                 $descricao  = trim($arBens[$i]["descricao"]);
                 $html .= '<tr>';
                 $html .= '      <td class="labelcenter">'.$cont++.'</td>';
                 $html .= '      <td class="show_dados_right">'.$codBem.'</td>';
                 $html .= '      <td class="show_dados">'.$nomEspecie.'</td>';
                 $html .= '      <td class="show_dados">'.$descricao.'</td>';
                 $html .= '      <td class=botao width=5>';
                 $html .= '      <a href="JavaScript:excluiDado('.$codBem.');">';
                 $html .= '      <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif" title="Excluir" border="0"></a>';
                 $html .= '</tr>';
                }
       }

       $html .= '</table>';


       sistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '$html';");
    break;

    case 7:
       $html = '<table width="100%">';
       $html .= '<tr>';
       $html .= '<td class="alt_dados" colspan="5">Bens da Apólice</td>';
       $html .= '</tr>';

       $html .= '<tr>';
       $html .= '    <td class="labelcenter" width="5%">&nbsp;</td>';
       $html .= '    <td class="labelcenter" width="10%">Código</td>';
       $html .= '    <td class="labelcenter" width="30%">Espécie</td>';
       $html .= '    <td class="labelcenter" width="55%">Descrição</td>';
       $html .= '    <td class="labelcenter" width="3%">&nbsp;</td>';
       $html .= '</tr>';


       $arBens = $sessao->arBens;
       foreach ($sessao->arBens as $arBem) {
            if ($arBem['codBem'] == $codBem) {
                $boJaConsta = true;
            }
       }
       if($boJaConsta)
           sistemaLegado::exibeAviso("O bem ".$codBem." já consta na Apólice ".$numApolice,"unica","erro");
       else {
            $sSQL = "
            SELECT DISTINCT
                  bae.cod_bem,
                  e.nom_especie,
                  b.descricao
            FROM
                  patrimonio.bem as bae,
                  patrimonio.especie as e,
                  patrimonio.vw_bem_ativo as b
            WHERE
                  bae.cod_bem = ".$codBem." AND
                  bae.cod_especie = e.cod_especie AND
                  bae.cod_grupo = e.cod_grupo AND
                  bae.cod_natureza = e.cod_natureza AND
                  bae.cod_bem = b.cod_bem";

            $dbEmp = new dataBaseLegado;
            $dbEmp->abreBD();
            $dbEmp->abreSelecao($sSQL);
            $dbEmp->vaiPrimeiro();
          if (!$dbEmp->eof()) {
             $arTmp = array();
             $arTmp['codBem'] = $codBem;
             $arTmp['nomEspecie'] = $dbEmp->pegaCampo('nom_especie');
             $arTmp['descricao'] =  $dbEmp->pegaCampo('descricao');
             $arTmp['descricao'] = str_replace('"', '&#034;', str_replace(chr(13).chr(10)," ",$arTmp['descricao']));
             $arTmp['descricao'] = str_replace('\'', '&#039;', str_replace(chr(13).chr(10)," ",$arTmp['descricao']));
             $arBens[] = $arTmp;
          } else {
             sistemaLegado::exibeAviso("O bem ".$codBem." não existe.","unica","erro");
          }
       }
       $sessao->arBens = $arBens;
       if (empty($arBens)) {
           $html.= '<tr>';
           $html.= '      <td class="show_dados" colspan="5">Ainda não há bens cadastrados nesta Apólice</td>';
           $html.= '</tr>';
       } else {
           $cont = 1;
           for ($i=0;$i<count($arBens);$i++) {
                 $codBem     = intval(trim($arBens[$i]["codBem"]));
                 $nomEspecie = trim($arBens[$i]["nomEspecie"]);
                 $descricao  = trim($arBens[$i]["descricao"]);
             $descricao = str_replace('"', '&#034;', str_replace(chr(13).chr(10)," ",$descricao));
             $descricao = str_replace('\'', '&#039;', str_replace(chr(13).chr(10)," ",$descricao));
                 $html .= '<tr>';
                 $html .= '      <td class="labelcenter">'.$cont++.'</td>';
                 $html .= '      <td class="show_dados_right">'.$codBem.'</td>';
                 $html .= '      <td class="show_dados">'.$nomEspecie.'</td>';
                 $html .= '      <td class="show_dados">'.$descricao.'</td>';
                 $html .= '      <td class=botao width=5>';
                 $html .= '      <a href="JavaScript:excluiDado('.$codBem.');">';
                 $html .= '      <img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/btnexcluir.gif" title="Excluir" border="0"></a>';                  $html .= '</tr>';
                }
       }

       $html .= '</table>';

       sistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '$html';");
   break;

}
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/legado/rodapeLegado.php';
?>
