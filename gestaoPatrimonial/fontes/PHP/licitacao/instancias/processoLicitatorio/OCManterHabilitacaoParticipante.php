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
    * Pagina Oculta para Formulário de ex
    * Data de Criação   : 19/10/2006ar

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Thiago La Delfa  Cabelleira

    * @ignore

    $Id: OCManterHabilitacaoParticipante.php 62136 2015-03-31 14:02:14Z michel $

    * Casos de uso: uc-03.05.19

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterHabilitacaoParticipante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $request->get('stCtrl');
$stJs   = '';

function geraListaDocumentos($obj)
{
    include_once ( TLIC."TLicitacaoCertificacaoDocumentos.class.php" );

    $arDocumentoParticipante = Sessao::read('arDocumentoParticipante_'.$obj->getDado('numcgm'));

    if (!$arDocumentoParticipante) {
        include_once(TLIC."TLicitacaoLicitacaoDocumentos.class.php");
        include_once(TLIC."TLicitacaoParticipanteDocumentos.class.php");
        
        $rsLicitacaoEdital = new RecordSet();
        $rsLicitacaoEdital->setCampo('num_edital'    , $obj->getDado('num_edital')     );
        $rsLicitacaoEdital->setCampo('exercicio'     , $obj->getDado('exercicio')      );
        $rsLicitacaoEdital->setCampo('cod_licitacao' , $obj->getDado('cod_licitacao')  );
        $rsLicitacaoEdital->setCampo('cod_modalidade', $obj->getDado('cod_modalidade') );
        $rsLicitacaoEdital->setCampo('cod_entidade'  , $obj->getDado('cod_entidade')   );
        
        $obTLicitacaoDocumentos = new TLicitacaoLicitacaoDocumentos();

        $obTLicitacaoDocumentos->setDado('cod_licitacao',$rsLicitacaoEdital->getCampo('cod_licitacao'));
        $obTLicitacaoDocumentos->setDado('cod_modalidade',$rsLicitacaoEdital->getCampo('cod_modalidade'));
        $obTLicitacaoDocumentos->setDado('cod_entidade',$rsLicitacaoEdital->getCampo('cod_entidade'));
        $obTLicitacaoDocumentos->setDado('exercicio',$rsLicitacaoEdital->getCampo('exercicio'));
        $obTLicitacaoDocumentos->recuperaDocumentosLicitacao($rsDocumentos);

        $inCount = 0;
        while (!$rsDocumentos->eof()) {

            $obTLicitacaoParticipanteDocumentos = new TLicitacaoParticipanteDocumentos();
            $obTLicitacaoParticipanteDocumentos->setDado('cod_licitacao' ,$rsLicitacaoEdital->getCampo('cod_licitacao'));
            $obTLicitacaoParticipanteDocumentos->setDado('cod_entidade'  ,$rsLicitacaoEdital->getCampo('cod_entidade'));
            $obTLicitacaoParticipanteDocumentos->setDado('cod_modalidade',$rsLicitacaoEdital->getCampo('cod_modalidade'));
            $obTLicitacaoParticipanteDocumentos->setDado('exercicio'	 ,$rsLicitacaoEdital->getCampo('exercicio'));
            $obTLicitacaoParticipanteDocumentos->setDado('cod_documento' ,$rsDocumentos->getCampo('cod_documento'));
            $obTLicitacaoParticipanteDocumentos->setDado('cgm_fornecedor',$obj->getDado('numcgm'));
            $obTLicitacaoParticipanteDocumentos->recuperaPorChave( $rsParticipanteDocumento );

            if ( !$rsParticipanteDocumento->getCampo('num_documento') ) {

                // buscando documento na tabela certificacao documentos
                $obTLicitacaoCertificacaoDocumentos = new TLicitacaoCertificacaoDocumentos;
                $obTLicitacaoCertificacaoDocumentos->setDado( 'cod_documento' , $rsDocumentos->getCampo('cod_documento') );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'cgm_fornecedor', $obj->getDado('numcgm')                  );
                $obTLicitacaoCertificacaoDocumentos->setDado( 'exercicio'     , Sessao::getExercicio()                       );
                $obTLicitacaoCertificacaoDocumentos->recuperaPorChave( $rsParticipanteDocumento );
            }
            $arDocumentoParticipante[$inCount]['id'				] = $inCount+1;
            $arDocumentoParticipante[$inCount]['numcgm'			] = $obj->getDado('numcgm'								);
            $arDocumentoParticipante[$inCount]['cod_documento'	] = $rsDocumentos->getCampo('cod_documento'				);
            $arDocumentoParticipante[$inCount]['nom_documento'	] = $rsDocumentos->getCampo('nom_documento'				);
            $arDocumentoParticipante[$inCount]['num_documento'	] = $rsParticipanteDocumento->getCampo('num_documento'	);
            $arDocumentoParticipante[$inCount]['dt_emissao'		] = $rsParticipanteDocumento->getCampo('dt_emissao'		);
            $arDocumentoParticipante[$inCount]['dt_validade'	] = $rsParticipanteDocumento->getCampo('dt_validade'	);
            $inCount++;
            $rsDocumentos->proximo();
        }

        Sessao::write('arDocumentoParticipante_'.$obj->getDado('numcgm'), $arDocumentoParticipante);
    }

    return montaListaDocumentoParticipante( $arDocumentoParticipante );
}

function montaListaParticipante($arRecordSet ,$boExecuta = true)
{
    global $pgOcul;

    $rsRecordSet = new RecordSet();
    $rsRecordSet->preenche($arRecordSet);
    $rsRecordSet->setPrimeiroElemento();

    $obListaParticipante = new Lista;
    $obListaParticipante->setTitulo('Participantes');
    $obListaParticipante->setMostraPaginacao( true );
    $obListaParticipante->setRecordSet( $rsRecordSet );

    $obListaParticipante->addCabecalho();
    $obListaParticipante->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaParticipante->ultimoCabecalho->setWidth( 5 );
    $obListaParticipante->commitCabecalho();

    $obListaParticipante->addCabecalho();
    $obListaParticipante->ultimoCabecalho->addConteudo("Participante");
    $obListaParticipante->ultimoCabecalho->setWidth( 85 );
    $obListaParticipante->commitCabecalho();
    $obListaParticipante->addDado();
    $obListaParticipante->ultimoDado->setCampo( "nom_cgm" );
    $obListaParticipante->commitDadoComponente();

    $obListaParticipante->addCabecalho();
    $obListaParticipante->ultimoCabecalho->addConteudo("Selecione");
    $obListaParticipante->ultimoCabecalho->setWidth( 5 );
    $obListaParticipante->commitCabecalho();
    $obListaParticipante->addDado();

    $obChkListaParticipante = new Radio();
    $obChkListaParticipante->setName ('documentosParticipante');
    $obChkListaParticipante->setId('documentosParticipante');
    $obChkListaParticipante->setValue ('numcgm');
    $obChkListaParticipante->obEvento->setOnClick("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&num_edital=".$arRecordSet[0]['num_edital']."&inCodLicitacao=".$arRecordSet[0]['cod_licitacao']."&inCodModalidade=".$arRecordSet[0]['cod_modalidade']."&inCodEntidade=".$arRecordSet[0]['cod_entidade']."&stExercicio=".$arRecordSet[0]['exercicio']."&numcgm='+this.value,'validaPadrao');");
    
    $obListaParticipante->addDadoComponente( $obChkListaParticipante );
    $obListaParticipante->ultimoDado->setCampo( "padrao" );
    $obListaParticipante->ultimoDado->setNameSequencial(false);
    $obListaParticipante->ultimoDado->setAlinhamento('CENTRO');
    $obListaParticipante->commitDadoComponente();
    $obListaParticipante->setMostraPaginacao(false);
    
    if ( count($arRecordSet) > 5 ) {
            $obListaParticipante->setMostraScroll(145);
    }

    $obListaParticipante->montaHTML();
    $stHTML = $obListaParticipante->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
            $stJs = "parent.frames['telaPrincipal'].document.getElementById('spnListaParticipante').innerHTML = '".$stHTML."';\n";

            return $stJs;
    } else {
            return $stHTML;
    }

}// fim do montaListaParticipante

function montaListaDocumentoParticipante($arRecordSet ,$boExecuta = true)
{
    global $pgOcul;

    $rsRecordSet = new RecordSet();

    if ( !is_array( $arRecordSet ) ) {
            $arRecordSet = array();
    }

    $rsRecordSet->preenche($arRecordSet);
    $rsRecordSet->setPrimeiroElemento();

    $obListaDocumentoParticipante = new Lista;
    $obListaDocumentoParticipante->setTitulo("Documentos do Participante: ".$rsRecordSet->getCampo('nom_cgm'));
    $obListaDocumentoParticipante->setMostraPaginacao( false );
    $obListaDocumentoParticipante->setRecordSet( $rsRecordSet );

    $obListaDocumentoParticipante->addCabecalho();
    $obListaDocumentoParticipante->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaDocumentoParticipante->ultimoCabecalho->setWidth( 3 );
    $obListaDocumentoParticipante->commitCabecalho();

    $obListaDocumentoParticipante->addCabecalho();
    $obListaDocumentoParticipante->ultimoCabecalho->addConteudo("Documento");
    $obListaDocumentoParticipante->commitCabecalho();

    $obListaDocumentoParticipante->addDado();
    $obListaDocumentoParticipante->ultimoDado->setCampo( "nom_documento" );
    $obListaDocumentoParticipante->ultimoDado->setAlinhamento( 'LEFT' );
    $obListaDocumentoParticipante->commitDadoComponente();

    $obListaDocumentoParticipante->addCabecalho();
    $obListaDocumentoParticipante->ultimoCabecalho->addConteudo("Emissão");
    $obListaDocumentoParticipante->ultimoCabecalho->setWidth( 10 );
    $obListaDocumentoParticipante->commitCabecalho();

    $obListaDocumentoParticipante->addDado();
    $obListaDocumentoParticipante->ultimoDado->setCampo( "dt_emissao" );
    $obListaDocumentoParticipante->ultimoDado->setAlinhamento( 'LEFT' );
    $obListaDocumentoParticipante->commitDadoComponente();

    $obListaDocumentoParticipante->addCabecalho();
    $obListaDocumentoParticipante->ultimoCabecalho->addConteudo("Validade");
    $obListaDocumentoParticipante->ultimoCabecalho->setWidth( 10 );
    $obListaDocumentoParticipante->commitCabecalho();

    $obListaDocumentoParticipante->addDado();
    $obListaDocumentoParticipante->ultimoDado->setCampo( "dt_validade" );
    $obListaDocumentoParticipante->ultimoDado->setAlinhamento( 'LEFT' );
    $obListaDocumentoParticipante->commitDadoComponente();

    $obListaDocumentoParticipante->addCabecalho();
    $obListaDocumentoParticipante->ultimoCabecalho->addConteudo("Número");
    $obListaDocumentoParticipante->ultimoCabecalho->setWidth( 15 );
    $obListaDocumentoParticipante->commitCabecalho();

    $obListaDocumentoParticipante->addDado();
    $obListaDocumentoParticipante->ultimoDado->setCampo( "num_documento" );
    $obListaDocumentoParticipante->ultimoDado->setAlinhamento( 'LEFT' );
    $obListaDocumentoParticipante->commitDadoComponente();

    $obListaDocumentoParticipante->addCabecalho();
    $obListaDocumentoParticipante->ultimoCabecalho->addConteudo("Ação");
    $obListaDocumentoParticipante->ultimoCabecalho->setWidth( 5 );
    $obListaDocumentoParticipante->commitCabecalho();

    $obListaDocumentoParticipante->addAcao();
    $obListaDocumentoParticipante->ultimaAcao->setAcao( 'ALTERAR' );
    $obListaDocumentoParticipante->ultimaAcao->setFuncaoAjax( true );
    $obListaDocumentoParticipante->ultimaAcao->setLink("javascript:executaFuncaoAjax('carregaAlterar');");
    $obListaDocumentoParticipante->ultimaAcao->addCampo("1","cod_documento");
    $obListaDocumentoParticipante->ultimaAcao->addCampo("2","nom_documento");
    $obListaDocumentoParticipante->ultimaAcao->addCampo("3","numcgm");
    $obListaDocumentoParticipante->ultimaAcao->addCampo("4","id");
    $obListaDocumentoParticipante->ultimaAcao->addCampo("5","dt_validade");
    $obListaDocumentoParticipante->ultimaAcao->addCampo("6","dt_emissao");
    $obListaDocumentoParticipante->ultimaAcao->addCampo("7","num_documento");
    $obListaDocumentoParticipante->commitAcao();

    $obListaDocumentoParticipante->montaHTML();
    $stHTML = $obListaDocumentoParticipante->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
            $stJs= "parent.frames['telaPrincipal'].document.getElementById('spnListaDocumentoParticipante').innerHTML = '".$stHTML."';\n";

            return $stJs;
    } else {
            return $stHTML;
    }
}// fim do montaListaDocumentoParticipante

function sincronizarDataValidaDocumento($inDiasValidos, $inDataEmissao)
{
    if ($inDataEmissao != "") {

        if ($inDiasValidos != "") {
            $diasValidos = $inDiasValidos;
        } else {
            $diasValidos = 0;
        }

        $arDataEmissao = explode('/',$inDataEmissao);
        //defino data de emissao
        $ano = $arDataEmissao[2];
        $mes = $arDataEmissao[1];
        $dia = $arDataEmissao[0];

        $dataEmissao = mktime(0,0,0,$mes,$dia,$ano);

        $dataValidade = strftime("%d/%m/%Y" , strtotime("+".$diasValidos." days",$dataEmissao));

        $stJs .= "jQuery('#dt_validade').val('".$dataValidade."');\n";
        $stJs .= "jQuery('#inNumDiasValido').val('".$diasValidos."');\n";
    }

    return $stJs;
}

function sincronizaDiasValidosDocumento($inDataValidade, $inDataEmissao)
{
    $stJs = "";

    if (strlen($inDataValidade) == 10) {

        if ($inDataValidade != "") {
            $arDataValidade = explode('/',$inDataValidade);
            $dataValidade = $inDataValidade;
        } else {
            $arDataValidade = explode('/',date('d/m/Y'));
            $dataValidade = date('d/m/Y');
        }

         //defino data de validade
        $ano1 = $arDataValidade[2];
        $mes1 = $arDataValidade[1];
        $dia1 = $arDataValidade[0];

        //defino data de emissão
        $arDtEmissao = explode('/',$inDataEmissao);
        $ano2 = $arDtEmissao[2];
        $mes2 = $arDtEmissao[1];
        $dia2 = $arDtEmissao[0];

        //calculo timestam das duas datas
        $timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
        $timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

        //diminuo a uma data a outra
        $segundos_diferenca = $timestamp1 - $timestamp2;

        //converto segundos em dias
        $diasValido = $segundos_diferenca / (60 * 60 * 24);

        //obtenho o valor absoluto dos dias (tiro o possível sinal negativo)
        $diasValido = abs($diasValido);

        //tiro os decimais aos dias de diferenca
        $diasValido = floor($diasValido);

        $stJs .= "jQuery('#dt_validade').val('');\n";
        $stJs .= "jQuery('#dt_validade').val('".$dataValidade."');\n";
        $stJs .= "jQuery('#inNumDiasValido').val('".$diasValido."');\n";
    } else {
        $stJs .= "jQuery('#dt_validade').val('');\n";
        $stJs .= "jQuery('#inNumDiasValido').val('');\n";
    }

    return $stJs;

}

switch ($stCtrl) {
    case 'exibeParticipante':
        
        include_once(TLIC."TLicitacaoParticipante.class.php");
        $obTLicitacaoParticipante = new TLicitacaoParticipante();
        
        $obTLicitacaoParticipante->setDado('exercicio'      , $request->get('stExercicioLicitacao') );
        $obTLicitacaoParticipante->setDado('cod_licitacao'  , $request->get('inCodLicitacao')       );
        $obTLicitacaoParticipante->setDado('cod_modalidade' , $request->get('inCodModalidade')      );
        $obTLicitacaoParticipante->setDado('cod_entidade'   , $request->get('inCodEntidade')        );
        
        
            $obTLicitacaoParticipante->recuperaParticipanteLicitacaoHabilitacao($rsParticipante);

            $obSpnNumLicitacao = new Label();
            $obSpnNumLicitacao->setValue( $rsParticipante->getCampo('cod_licitacao').'/'.$rsParticipante->getCampo('exercicio') );
            $obSpnNumLicitacao->setRotulo('Número da Licitação');

            $obLblEntidade = new Label();
            $obLblEntidade->setValue( $rsParticipante->getCampo('cod_entidade').' - '.$rsParticipante->getCampo('nom_entidade') );
            $obLblEntidade->setRotulo( 'Entidade' );

            $obLblModalidade = new Label();
            $obLblModalidade->setValue( $rsParticipante->getCampo('cod_modalidade').' - '.$rsParticipante->getCampo('nom_modalidade') );
            $obLblModalidade->setRotulo( 'Modalidade' );

            include_once(CAM_GP_COM_COMPONENTES."ILabelEditObjeto.class.php");
            $obIlabelEditObjeto = new ILabelEditObjeto();
            $obIlabelEditObjeto->setCodObjeto($rsParticipante->getCampo('cod_objeto'));
            $obIlabelEditObjeto->setRotulo('Objeto');

            $obFormulario = new Formulario();
            $obFormulario->addComponente($obLblEntidade);
            $obFormulario->addComponente($obSpnNumLicitacao);
            $obFormulario->addComponente($obIlabelEditObjeto);
            $obFormulario->addComponente($obLblModalidade);
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();
            
            $stJs.="d.getElementById('spnNumeroLicitacao').innerHTML = '".$stHTML."';           \n";
            $stJs.="f.cod_licitacao.value  = '".$rsParticipante->getCampo('cod_licitacao')."';  \n";
            $stJs.="f.cod_modalidade.value = '".$rsParticipante->getCampo('cod_modalidade')."'; \n";
            $stJs.="f.cod_entidade.value   = '".$rsParticipante->getCampo('cod_entidade')."';   \n";
            $stJs.="f.exercicio.value      = '".$rsParticipante->getCampo('exercicio')."';      \n";
            $stJs.="f.numcgm.value         = '".$rsParticipante->getCampo('numcgm')."';         \n";

            $inCount = 0;
            $arParticipante = array();
            while (!$rsParticipante->eof()) {
                    $arParticipante[$inCount]['nom_cgm']        = $rsParticipante->getCampo('numcgm').' - '.$rsParticipante->getCampo('nom_cgm');
                    $arParticipante[$inCount]['numcgm']         = $rsParticipante->getCampo('numcgm');
                    $arParticipante[$inCount]['cod_modalidade'] = $rsParticipante->getCampo('cod_modalidade');
                    $arParticipante[$inCount]['exercicio']      = $rsParticipante->getCampo('exercicio');
                    $arParticipante[$inCount]['cod_licitacao']  = $rsParticipante->getCampo('cod_licitacao');
                    $arParticipante[$inCount]['num_edital']     = $rsParticipante->getCampo('num_edital');
                    $arParticipante[$inCount]['exercicio']      = $rsParticipante->getCampo('exercicio');
                    $arParticipante[$inCount]['cod_entidade']   = $rsParticipante->getCampo('cod_entidade');

                    $inCount++;
                    Sessao::remove('arDocumentoParticipante_'.$rsParticipante->getCampo('numcgm'));
                    $rsParticipante->proximo();
            }//fim do while

            // se a lista tem participante, seleciona o primeiro
            if (count($arParticipante > 0)) {
                    $arParticipante[0]['padrao'] = true;
            } else {
                    $stJs.= montaListaParticipante( $arParticipante );
                    break;
            } //nao há participantes , nem vamos buscar os documentos
            //mostra a lista de participantes

            $obTLicitacaoParticipante->setDado('numcgm',$arParticipante[0]['numcgm']);
            $obTLicitacaoParticipante->setDado('cod_modalidade', $arParticipante[0]['cod_modalidade']);
            $obTLicitacaoParticipante->setDado('cod_entidade'  , $arParticipante[0]['cod_entidade']);
            $obTLicitacaoParticipante->setDado('exercicio'     , $arParticipante[0]['exercicio']);
            $obTLicitacaoParticipante->setDado('cod_licitacao' , $arParticipante[0]['cod_licitacao']);
            $obTLicitacaoParticipante->setDado('num_edital'    , $arParticipante[0]['num_edital']);
            
            Sessao::write('arParticipante', $arParticipante);

            $stJs.= montaListaParticipante( $arParticipante );
            $stJs.= geraListaDocumentos($obTLicitacaoParticipante);

    break;

    case "sincronizaDataValida":
        $stJs.= sincronizarDataValidaDocumento($_REQUEST['inNumDiasValido'], $_REQUEST['dt_emissao']);
    break;

    case "sincronizaDiasValidos":
        $stJs.= sincronizaDiasValidosDocumento($_REQUEST['dt_validade'], $_REQUEST['dt_emissao']);
    break;

    case 'validaPadrao':

            $stJs.="
                    var inputs = window.document.getElementsByTagName('input');
                    for (var i=0; i < inputs.length; i++) { //iterate through all input elements
                      if (inputs[i].type.toLowerCase() == 'checkbox' && inputs[i].value!=".$_REQUEST['numcgm'].") { //if the element is a checkbox
                        inputs[i].checked = false;
                      } else if (inputs[i].value==".$_REQUEST['numcgm'].") {
                        inputs[i].checked = true;
                      }
                    }";
            $stJs .="f.numcgm.value = '".$_REQUEST['numcgm']."';\n";

            include_once(TLIC."TLicitacaoParticipante.class.php");
            $obTLicitacaoParticipante = new TLicitacaoParticipante();
            
            $obTLicitacaoParticipante->setDado('numcgm'         , $_REQUEST['numcgm']);
            $obTLicitacaoParticipante->setDado('num_edital'     , $_REQUEST['num_edital']);
            $obTLicitacaoParticipante->setDado('exercicio'      , $_REQUEST['stExercicio']);
            $obTLicitacaoParticipante->setDado('cod_entidade'   , $_REQUEST['inCodEntidade']);
            $obTLicitacaoParticipante->setDado('cod_modalidade' , $_REQUEST['inCodModalidade']);
            $obTLicitacaoParticipante->setDado('cod_licitacao'  , $_REQUEST['inCodLicitacao']);            
            
            $stJs.=geraListaDocumentos($obTLicitacaoParticipante);

        break;

    case "limpar":
            Sessao::write('arParticipante', array());
            Sessao::write('arDocumentoParticipante', array());
        break;

    case "carregaAlterar":

        $inCount = 0;
        $arFornecedores = array();
        $inCodCgmFornecedor = $_REQUEST['numcgm'];

        include_once CAM_GP_COM_MAPEAMENTO.'TComprasFornecedor.class.php';
        $obTComprasFornecedor = new TComprasFornecedor();
        $obTComprasFornecedor->setDado("cgm_fornecedor", $inCodCgmFornecedor);
        $obTComprasFornecedor->recuperaListaFornecedor( $rsFornecedor );

        if ($rsFornecedor->getCampo('status') == 'Inativo') {
            $arFornecedores[$inCount]['cgm_fornecedor'] = $inCodCgmFornecedor;
            $arFornecedores[$inCount]['nom_cgm'] = $rsFornecedor->getCampo('nom_cgm');
            $inCount++;
        }

        if (count($arFornecedores) > 0) {
            if (count($arFornecedores) == 1) {
                $stMensagemErro = 'O Participante ('.$arFornecedores[0]['cgm_fornecedor'].' - '.$arFornecedores[0]['nom_cgm'].') está inativo! Efetue a Manutenção de Participantes para retirar este Participante.';
            } elseif (count($arFornecedores) > 1) {
                foreach ($arFornecedores as $arFornecedoresAux) {
                    $stCodNomFornecedores .= $arFornecedoresAux['cgm_fornecedor'].' - '.$arFornecedoresAux['nom_cgm'].', ';
                }
                $stCodNomFornecedores = substr($stCodNomFornecedores, 0, strlen($stCodNomFornecedores)-2);
                $stMensagemErro = 'Os Participantes ('.$stCodNomFornecedores.') estão inativos! Efetue a Manutenção de Participantes para retirar estes Participantes.';
            }
        }

        if (!$stMensagemErro) {
            $obForm = new Form;
            $obForm->setAction                  ( $pgProc );
            $obForm->setTarget                  ( "oculto" );

            $obFormulario = new Formulario;
            $obFormulario->addForm          ( $obForm                        );

            $obFormulario->setAjuda         ("UC-03.05.19"                   );

            $obLblDocumento = new Label();
            $obLblDocumento->setRotulo('Documento');
            $obLblDocumento->setValue($_REQUEST['nom_documento']);

            $obNumDocumento = new TextBox();
            $obNumDocumento->setName('num_documento');
            $obNumDocumento->setId('num_documento');
            $obNumDocumento->setRotulo('Número do Documento');
            $obNumDocumento->setValue($_REQUEST['num_documento']);
            $obNumDocumento->setMaxLength(30);
            $obNumDocumento->setNull ( false );

            $obDataEmissao = new Data();
            $obDataEmissao->setName('dt_emissao');
            $obDataEmissao->setId('dt_emissao');
            $obDataEmissao->setRotulo('Data de Emissão');
            $obDataEmissao->setValue($_REQUEST['dt_emissao']);
            $obDataEmissao->setNull ( false );
            $obDataEmissao->obEvento->setOnChange("bloqueiaDesbloqueiaCampos(this);formataDiasValidosDocumento();");

            $obDataValidade = new Data();
            $obDataValidade->setName ( "dt_validade" );
            $obDataValidade->setId ( "dt_validade" );
            $obDataValidade->setValue( $_REQUEST['dt_validade'] );
            $obDataValidade->setRotulo( "Data de Validade" );
            $obDataValidade->setTitle( "Informe a Data de Validade do Documento." );
            $obDataValidade->obEvento->setOnChange("if (verificaData(this)) { if (validaData(this)) { formataDiasValidosDocumento(); } } else { jQuery(this).val(''); jQuery('#inNumDiasValido').val(''); jQuery('#inNumDiasValido').val(''); }");
            $obDataValidade->setNull( false );
            if ($_REQUEST['dt_emissao'] == "") {
                $obDataValidade->setDisabled(true);
            } else {
                $obDataValidade->setDisabled(false);
            }

            $obTxtNumDiasVcto = new TextBox;
            $obTxtNumDiasVcto->setName  ( "inNumDiasValido" );
            $obTxtNumDiasVcto->setId  ( "inNumDiasValido" );
            $obTxtNumDiasVcto->setRotulo( "Dias para Vencimento" );
            $obTxtNumDiasVcto->setTitle ( "Informe o número de dias para o vencimento do documento." );
            $obTxtNumDiasVcto->setValue ( $_REQUEST['inNumDiasValido'] );
            $obTxtNumDiasVcto->setMaxLength(4);
            $obTxtNumDiasVcto->setInteiro(true);
            $obTxtNumDiasVcto->setObrigatorioBarra( false );
            if ($_REQUEST['dt_emissao'] == "") {
                $obTxtNumDiasVcto->setDisabled(true);
            } else {
                $obTxtNumDiasVcto->setDisabled(false);
            }
            $obTxtNumDiasVcto->obEvento->setOnBlur('formataDataValidaDocumento()');

            $obFormulario->addTitulo        ( "Manutenção de Documento de Participante"  );
            $obFormulario->addComponente( $obLblDocumento	);
            $obFormulario->addComponente( $obNumDocumento 	);
            $obFormulario->addComponente( $obDataEmissao 	);
            $obFormulario->addComponente( $obTxtNumDiasVcto );
            $obFormulario->addComponente( $obDataValidade 	);

            $obBtAlterar = new Button();
            $obBtAlterar->setName('btAlterar');
            $obBtAlterar->setValue('Alterar');
            $obBtAlterar->obEvento->setOnClick("javascript:ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&id=".$_REQUEST['id']."&numcgm=".$_REQUEST['numcgm']."&cod_documento=".$_REQUEST['cod_documento']."&dt_emissao='+getElementById('dt_emissao').value+'&dt_validade='+getElementById('dt_validade').value+'&num_documento='+getElementById('num_documento').value,'incluirDocumento');");

            $obBtCancelar = new Button();
            $obBtCancelar->setName('btCancelar');
            $obBtCancelar->setValue('Cancelar');
            $obBtCancelar->obEvento->setOnClick('javascript:LimparAlterarDocumento();');

            $obFormulario->defineBarra( array($obBtAlterar, $obBtCancelar) );

            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();

            $stJs .= "document.getElementById('spnAlterarDocumentoParticipante').innerHTML = '".$stHTML."';";

            if ( ($_REQUEST['dt_validade'] != "") && ($_REQUEST['dt_emissao'] != "")) {
                $stJs.= sincronizaDiasValidosDocumento($_REQUEST['dt_validade'], $_REQUEST['dt_emissao']);
            }
        } else {
            $stJs = "alertaAviso( '".$stMensagemErro."' , 'form', 'erro' , '" . Sessao::getId() . "' );\n";
        }

        break;

    case 'incluirDocumento' :

            if ( implode(array_reverse(explode('/',$_REQUEST['dt_emissao']))) > date('Ymd') ) {
                    $stMensagem = 'A data de emissão não pode ser superior a de hoje.';
            }
            //if ( implode(array_reverse(explode('/',$_REQUEST['dt_validade']))) < date('Ymd') ) {
            //        $stMensagem = 'A data de validade deve ser maior ou igual que a de hoje.';
            //}
            if ( trim($_REQUEST['num_documento']) == '' ) {
                    $stMensagem = 'Número do documento inválido.';
            }
            if (!$_REQUEST['dt_emissao']) {
                $stMensagem = 'Preencha a data de emissão.';
            }

            $arDocumentoParticipante = Sessao::read('arDocumentoParticipante_'.$_REQUEST['numcgm']);
            if (!$arDocumentoParticipante) {
                    $arDocumentoParticipante = array();
            }
            if ($stMensagem == '') {
                    $id = $_REQUEST['id']-1;
                    $arDocumentoParticipante[$id]['dt_emissao'		] = $_REQUEST['dt_emissao'		];
                    $arDocumentoParticipante[$id]['dt_validade'		] = $_REQUEST['dt_validade'		];
                    $arDocumentoParticipante[$id]['num_documento'	] = $_REQUEST['num_documento'	];
                    $stJs = montaListaDocumentoParticipante($arDocumentoParticipante);
                    $stJs.= 'javascript:LimparAlterarDocumento();';
            } else {
                    $stJs .= "alertaAviso( '".$stMensagem."','form','erro','".Sessao::getId()."' );";
            }
            Sessao::write('arDocumentoParticipante_'.$_REQUEST['numcgm'], $arDocumentoParticipante);
    break;
}// fim do SWITCH

echo $stJs;

?>