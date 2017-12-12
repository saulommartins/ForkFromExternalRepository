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
 /**printlimpa
    * Pagina Oculta para Formulário de ex
    * Data de Criação   : 19/10/2006ar

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Thiago La Delfa  Cabelleira

    * @ignore

    * Casos de uso: uc-03.04.29

    $Id: OCManterNotaCompra.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterNotaCompra";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'] ?  $_REQUEST['stCtrl'] : $_REQUEST['stCtrl'];

function montaListaItens($arRecordSet ,$boExecuta = true)
{
    global $pgOcul;
    global $pgForm;

    $rsRecordSet = new RecordSet();

    $rsRecordSet->preenche($arRecordSet);
    $rsRecordSet->setPrimeiroElemento();

    $rsRecordSet->addFormatacao( "valor_un" , "NUMERIC_BR");
    $rsRecordSet->addFormatacao( "vl_total" , "NUMERIC_BR");

    $obListaItens = new Lista;
    $obListaItens->setTitulo('Ítens');
    $obListaItens->setMostraPaginacao( false );
    $obListaItens->setRecordSet( $rsRecordSet );

    $obListaItens->addCabecalho();
    $obListaItens->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaItens->ultimoCabecalho->setWidth( 5 );
    $obListaItens->commitCabecalho();

    $obListaItens->addCabecalho();
    $obListaItens->ultimoCabecalho->addConteudo("Ítem");
    $obListaItens->ultimoCabecalho->setWidth( 45 );
    $obListaItens->commitCabecalho();
    $obListaItens->addDado();
    $obListaItens->ultimoDado->setCampo( "nom_item" );
    $obListaItens->commitDadoComponente();

    $obListaItens->addCabecalho();
    $obListaItens->ultimoCabecalho->addConteudo("Centro de C");
    $obListaItens->ultimoCabecalho->setWidth( 12 );
    $obListaItens->commitCabecalho();
    $obListaItens->addDado();
    $obListaItens->ultimoDado->setCampo( "descricao" );
    $obListaItens->commitDadoComponente();

    $obListaItens->addCabecalho();
    $obListaItens->ultimoCabecalho->addConteudo("Qtde Emp");
    $obListaItens->ultimoCabecalho->setWidth( 5 );
    $obListaItens->commitCabecalho();
    $obListaItens->addDado();
    $obListaItens->ultimoDado->setCampo( "quantidade_emp" );
    $obListaItens->ultimoDado->setAlinhamento( 'DIREITA' );
    $obListaItens->commitDadoComponente();

    $obListaItens->addCabecalho();
    $obListaItens->ultimoCabecalho->addConteudo("Qtde Saldo");
    $obListaItens->ultimoCabecalho->setWidth( 5 );
    $obListaItens->commitCabecalho();
    $obListaItens->addDado();
    $obListaItens->ultimoDado->setCampo( "quantidade_saldo" );
    $obListaItens->ultimoDado->setAlinhamento( 'DIREITA' );
    $obListaItens->commitDadoComponente();

    $obListaItens->addCabecalho();
    $obListaItens->ultimoCabecalho->addConteudo("Valor Unitário");
    $obListaItens->ultimoCabecalho->setWidth( 14 );
    $obListaItens->commitCabecalho();
    $obListaItens->addDado();
    $obListaItens->ultimoDado->setCampo("valor_un");
    $obListaItens->ultimoDado->setAlinhamento( 'DIREITA' );
    $obListaItens->commitDadoComponente();

    $obListaItens->addCabecalho();
    $obListaItens->ultimoCabecalho->addConteudo("Qtde OC");
    $obListaItens->ultimoCabecalho->setWidth( 5 );
    $obListaItens->commitCabecalho();
    $obListaItens->addDado();
    $obListaItens->ultimoDado->setCampo( "quantidade_oc" );
    $obListaItens->ultimoDado->setAlinhamento( 'DIREITA' );
    $obListaItens->commitDadoComponente();

    $obListaItens->addCabecalho();
    $obListaItens->ultimoCabecalho->addConteudo("Valor Tt OC");
    $obListaItens->ultimoCabecalho->setWidth( 14 );
    $obListaItens->commitCabecalho();
    $obListaItens->addDado();
    $obListaItens->ultimoDado->setCampo( "vl_total" );
    $obListaItens->ultimoDado->setAlinhamento( 'DIREITA' );
    $obListaItens->commitDadoComponente();

    $obListaItens->montaHTML();
    $stHTML = $obListaItens->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
      $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnListaItens').innerHTML = '".$stHTML."';\n";

      return $stJs;
    } else {
      return $stHTML;
    }
}// fim do montaListaItens

switch ($stCtrl) {

 case 'exibeDadosNota':

   if (($_REQUEST['inCodEntidade'] == '' ) || ( $_REQUEST['inCodOrdemCompra'] == '') || ($_REQUEST['stExercicioOrdemCompra'] == '')) {
     $stJs .="alertaAviso( 'Informe todos os campos referentes à Ordem de Compra!','form','erro','".Sessao::getId()."' );";
     $stJs.= "parent.frames['telaPrincipal'].document.getElementById('inCodOrdemCompra').value = '';";
     $stJs.= "parent.frames['telaPrincipal'].document.getElementById('stExercicioOrdemCompra').value = '';";
     $stJs.= "parent.frames['telaPrincipal'].document.getElementById('inCodEntidadeOrdemCompra').value = '';";
     $stJs.= "parent.frames['telaPrincipal'].document.getElementById('inCodOrdemCompra').focus();";
     break;
   }

   include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");
   $obNF = new TComprasOrdemCompraNota();
   $obNF->setDado('cod_ordem',$_REQUEST['inCodOrdemCompra']);
   $obNF->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
   $obNF->setDado('exercicio',$_REQUEST['stExercicioOrdemCompra']);
   $obNF->pesquisaNF($rsNF);

   if ($rsNF->getCampo('cod_nota') != '') {

     $stJs .="alertaAviso( 'Essa ordem de compra já gerou uma nota fiscal!','form','erro','".Sessao::getId()."' );";
     $stJs.= "parent.frames['telaPrincipal'].document.getElementById('inCodOrdemCompra').value = '';";
     $stJs.= "parent.frames['telaPrincipal'].document.getElementById('stExercicioOrdemCompra').value = '';";
     $stJs.= "parent.frames['telaPrincipal'].document.getElementById('inCodEntidadeOrdemCompra').value = '';";
     $stJs.= "parent.frames['telaPrincipal'].document.getElementById('inCodOrdemCompra').focus();";
   } else {
     include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");
     $obDadosNota = new TComprasOrdemCompraNota();
     $obDadosNota->setDado('cod_ordem',$_REQUEST['inCodOrdemCompra']);
     $obDadosNota->recuperaDadosNota($rsDadosNota);

     $obLbEmpenho = new Label();
     $obLbEmpenho->setValue($rsDadosNota->getCampo('cod_empenho'));
     $obLbEmpenho->setRotulo('Número do Empenho');

     $obLbFornecedor = new Label();
     $obLbFornecedor->setValue($rsDadosNota->getCampo('nom_cgm'));
     $obLbFornecedor->setRotulo('Fornecedor');

     $obFormulario = new Formulario();
     $obFormulario->addComponente($obLbEmpenho);
     $obFormulario->addComponente($obLbFornecedor);
     $obFormulario->montaInnerHTML();
     $stHTML = $obFormulario->getHTML();

     $stJs .= "d.getElementById('spnDadosNota').innerHTML = '".$stHTML."';\n";
     $stJs.= "parent.frames['telaPrincipal'].document.getElementById('num_nota').focus();";

       $stJs .="f.hdnExercicio.value = '".$rsDadosNota->getCampo('exercicio')."';\n";
       $stJs .="f.hdnEntidade.value = '".$rsDadosNota->getCampo('cod_entidade')."';\n";
       $stJs .="f.hdnExercicioEmpenho.value = '".$rsDadosNota->getCampo('exercicio_empenho')."';\n";
       $stJs .="f.hdnEmpenho.value = '".$rsDadosNota->getCampo('cod_empenho')."';\n";
       $stJs .="f.hdnFornecedor.value = '".$rsDadosNota->getCampo('nom_cgm')."';\n";
       $stJs .="f.hdnNumFornecedor.value = '".$rsDadosNota->getCampo('numcgm')."';\n";

  }

 break;

 case 'exibeListaItens':

   include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");
   $obCodNota = new TComprasOrdemCompraNota();
   $obCodNota->setDado('num_serie',$_REQUEST['num_serie']);
   $obCodNota->setDado('num_nota',$_REQUEST['num_nota']);
   $obCodNota->recuperaCodNota($rsCodNota);

   include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");
   $obLista = new TComprasOrdemCompraNota();
   $obLista->setDado('cod_ordem',$_REQUEST['inCodOrdemCompra']);
   $obLista->setDado('exercicio',$_REQUEST['hdnExercicio']);
   $obLista->setDado('cod_entidade',$_REQUEST['hdnEntidade']);
   $obLista->setDado('exercicio_empenho',$_REQUEST['hdnExercicioEmpenho']);
   $obLista->setDado('cod_empenho',$_REQUEST['hdnEmpenho']);
   $obLista->recuperaItensNota($rsItens);

   //Array de DADOS da nota
   #sessao->transf3['arDadosNota'][0]['exercicio'] = $_REQUEST['hdnExercicio'];
   #sessao->transf3['arDadosNota'][0]['cod_entidade'] = $_REQUEST['hdnEntidade'];
   #sessao->transf3['arDadosNota'][0]['cod_ordem'] = $_REQUEST['inCodOrdemCompra'];
   #sessao->transf3['arDadosNota'][0]['cgm_fornecedor'] = $_REQUEST['hdnNumFornecedor'];
   #sessao->transf3['arDadosNota'][0]['cod_nota'] = $rsCodNota->getCampo('cod_nota');

    $arDadosNota[0]['exercicio'] = $_REQUEST['hdnExercicio'];
    $arDadosNota[0]['cod_entidade'] = $_REQUEST['hdnEntidade'];
    $arDadosNota[0]['cod_ordem'] = $_REQUEST['inCodOrdemCompra'];
    $arDadosNota[0]['cgm_fornecedor'] = $_REQUEST['hdnNumFornecedor'];
    $arDadosNota[0]['cod_nota'] = $rsCodNota->getCampo('cod_nota');

    Sessao::write('arDadosNota' , $arDadosNota);

   //Array de DADOS DOS ITENS da nota
   $inCount = 0;
   $arItens = array();
   while (!$rsItens->eof()) {
     $arItens[$inCount]['nom_item'] = $rsItens->getCampo('nom_item');
     $arItens[$inCount]['descricao'] = $rsItens->getCampo('descricao');
     $arItens[$inCount]['quantidade_emp'] = $rsItens->getCampo('quantidade_emp');
     $arItens[$inCount]['quantidade_saldo'] = $rsItens->getCampo('quantidade_emp') - $rsItens->getCampo('quantidade_oc');
     $arItens[$inCount]['valor_un'] = $rsItens->getCampo('vl_total') / $rsItens->getCampo('quantidade_emp');
     $arItens[$inCount]['quantidade_oc'] = $rsItens->getCampo('quantidade_oc');
     $arItens[$inCount]['vl_total'] = $arItens[$inCount]['valor_un']*$arItens[$inCount]['quantidade_oc'];
     $inCount++;
     $rsItens->proximo();
   }//fim do while
   Sessao::write('arItens' , $arItens);
   $stJs.= montaListaItens( $arItens );
   break;

  case 'consultarNotaCompra': //dados provenientes do LSManterNotaCompra

   include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");

   $filtro = Sessao::read('filtro');

   $obLista = new TComprasOrdemCompraNota();
   $obLista->setDado('cod_ordem', $filtro['cod_ordem']);
   $obLista->setDado('exercicio', $filtro['exercicio'] );
   $obLista->setDado('cod_entidade',$filtro['cod_entidade']);
   $obLista->setDado('exercicio_empenho', $filtro['exercicio_empenho']);
   $obLista->setDado('cod_empenho', $filtro['cod_empenho']);
   $obLista->recuperaItensNota($rsItens);

   //Array de DADOS DOS ITENS da nota
   $inCount = 0;
   $arItens = array();
   while (!$rsItens->eof()) {
     $arItens[$inCount]['nom_item'] = $rsItens->getCampo('nom_item');
     $arItens[$inCount]['descricao'] = $rsItens->getCampo('descricao');
     $arItens[$inCount]['quantidade_emp'] = $rsItens->getCampo('quantidade_emp');
     $arItens[$inCount]['quantidade_saldo'] = $rsItens->getCampo('quantidade_emp') - $rsItens->getCampo('quantidade_oc');
     $arItens[$inCount]['valor_un'] = $rsItens->getCampo('vl_total') / $rsItens->getCampo('quantidade_emp');
     $arItens[$inCount]['quantidade_oc'] = $rsItens->getCampo('quantidade_oc');
     $arItens[$inCount]['vl_total'] = $arItens[$inCount]['valor_un']*$arItens[$inCount]['quantidade_oc'];
     $inCount++;
     $rsItens->proximo();
   }//fim do while
   Sessao::write('arItens', $arItens);
   $stJs.= montaListaItens( $arItens );

  break;
}       // fim do SWITCH
echo $stJs;
?>
