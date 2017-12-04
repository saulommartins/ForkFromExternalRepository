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

  * Pagina Oculta para Formulário de
  * Data de Criação   : 05/10/2006

  * @author Analista: Cleisson da Silva Barboza
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @ignore

  * Casos de uso: uc-03.05.15

  $Id: OCManterProcessoLicitatorio.php 66174 2016-07-26 17:12:05Z lisiane $

  */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasMapa.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasMapaItem.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoNaturezaCargo.class.php";

global $boEdital;
$boEdital = Sessao::read('boEdital');


# Define o nome dos arquivos PHP
$stPrograma = "ManterProcessoLicitatorio";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgPror     = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];
$stJs="";

function montaListaMembroAdicional($arRecordSet , $boExecuta = true)
{
    if (!isset($stJs)) {
        $stJs="";
    }
    
    for($i=0;$i<count($arRecordSet);$i++){
        $stNatureza = '';
        $inCodNatureza = ($arRecordSet[$i]['cod_natureza_cargo']>-1) ? $arRecordSet[$i]['cod_natureza_cargo'] : 0;
        $obTLicitacaoNaturezaCargo = new TLicitacaoNaturezaCargo();
        $obTLicitacaoNaturezaCargo->recuperaTodos($rsNaturezaCargo, " WHERE codigo=".$inCodNatureza, "", $boTransacao);

        while (!$rsNaturezaCargo->eof()) {
            $stNatureza = trim($rsNaturezaCargo->getCampo('descricao'));

            $rsNaturezaCargo->proximo();
        }

        $arRecordSet[$i]['natureza_cargo'] = $stNatureza;        
    }

    $rsListaMembroAdicional = new RecordSet;

    $rsListaMembroAdicional->preenche( $arRecordSet );

    $rsListaMembroAdicional->setPrimeiroElemento();

    $rsListaMembroAdicional->ordena('num_cgm');

    $obListaMembroAdicional = new Lista;
    $obListaMembroAdicional->setTitulo('Membros Adicionais');
    $obListaMembroAdicional->setMostraPaginacao( false );

    $obListaMembroAdicional->setRecordSet( $rsListaMembroAdicional );

    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaMembroAdicional->ultimoCabecalho->setWidth( 3 );
    $obListaMembroAdicional->commitCabecalho();
    
    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("CGM");
    $obListaMembroAdicional->ultimoCabecalho->setWidth( 3);
    $obListaMembroAdicional->commitCabecalho();
    
    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("Nome");
    $obListaMembroAdicional->ultimoCabecalho->setWidth( 40 );
    $obListaMembroAdicional->commitCabecalho();
    
    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("Cargo");
    $obListaMembroAdicional->ultimoCabecalho->setWidth( 30 );
    $obListaMembroAdicional->commitCabecalho();

    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("Natureza do Cargo");
    $obListaMembroAdicional->ultimoCabecalho->setWidth( 13 );
    $obListaMembroAdicional->commitCabecalho();

    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("Adicional");
    $obListaMembroAdicional->ultimoCabecalho->setWidth( 5 );
    $obListaMembroAdicional->commitCabecalho();
    
    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaMembroAdicional->ultimoCabecalho->setWidth( 3 );
    $obListaMembroAdicional->commitCabecalho();
    
    $obListaMembroAdicional->addDado();
    $obListaMembroAdicional->ultimoDado->setCampo( "num_cgm" );
    $obListaMembroAdicional->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaMembroAdicional->commitDado();

    $obListaMembroAdicional->addDado();
    $obListaMembroAdicional->ultimoDado->setCampo( "nom_cgm" );
    $obListaMembroAdicional->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaMembroAdicional->commitDado();

    $obListaMembroAdicional->addDado();
    $obListaMembroAdicional->ultimoDado->setCampo( "cargo_membro" );
    $obListaMembroAdicional->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaMembroAdicional->commitDado();

    $obListaMembroAdicional->addDado();
    $obListaMembroAdicional->ultimoDado->setCampo( "natureza_cargo" );
    $obListaMembroAdicional->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaMembroAdicional->commitDado();

    $obListaMembroAdicional->addDado();
    $obListaMembroAdicional->ultimoDado->setCampo( "adicional" );
    $obListaMembroAdicional->ultimoDado->setAlinhamento( 'CENTRO' );
    $obListaMembroAdicional->commitDado();

    $obListaMembroAdicional->addAcao();
    $obListaMembroAdicional->ultimaAcao->setAcao( "ALTERAR" );
    $obListaMembroAdicional->ultimaAcao->setFuncaoAjax( true );
    $obListaMembroAdicional->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('alterarMembroAdicional');" );
    $obListaMembroAdicional->ultimaAcao->addCampo("1","num_cgm");
    $obListaMembroAdicional->commitAcao();

    $obListaMembroAdicional->addAcao();
    $obListaMembroAdicional->ultimaAcao->setAcao( "EXCLUIR" );
    $obListaMembroAdicional->ultimaAcao->setFuncaoAjax( true );
    $obListaMembroAdicional->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirMembroAdicional');" );
    $obListaMembroAdicional->ultimaAcao->addCampo("1","num_cgm");
    $obListaMembroAdicional->commitAcao();

    $obListaMembroAdicional->montaHTML();
    $stHTML = $obListaMembroAdicional->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnMembroAdicional').innerHTML = '".$stHTML."';";

        return $stJs;
    } else {
        return $stHTML;
    }
}

function montaListaMembroAdicionalConsulta($arRecordSet , $boExecuta = true)
{
    if (!isset($stJs)) {
        $stJs="";
    }
    $rsListaMembroAdicional = new RecordSet;

    $rsListaMembroAdicional->preenche( $arRecordSet );

    $rsListaMembroAdicional->setPrimeiroElemento();

    $rsListaMembroAdicional->ordena('num_cgm');

    $obListaMembroAdicional = new Lista;
    $obListaMembroAdicional->setTitulo('Membros Adicionais');
    $obListaMembroAdicional->setMostraPaginacao( false );

    $obListaMembroAdicional->setRecordSet( $rsListaMembroAdicional );

    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaMembroAdicional->ultimoCabecalho->setWidth( 3 );
    $obListaMembroAdicional->commitCabecalho();
    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("CGM");
    $obListaMembroAdicional->ultimoCabecalho->setWidth( 3);
    $obListaMembroAdicional->commitCabecalho();
    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("Nome");
    $obListaMembroAdicional->ultimoCabecalho->setWidth( 70 );
    $obListaMembroAdicional->commitCabecalho();
    $obListaMembroAdicional->addCabecalho();
    $obListaMembroAdicional->ultimoCabecalho->addConteudo("Adicional");
    $obListaMembroAdicional->ultimoCabecalho->setWidth(5 );
    $obListaMembroAdicional->commitCabecalho();

    $obListaMembroAdicional->addDado();
    $obListaMembroAdicional->ultimoDado->setCampo( "num_cgm" );
    $obListaMembroAdicional->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaMembroAdicional->commitDado();

    $obListaMembroAdicional->addDado();
    $obListaMembroAdicional->ultimoDado->setCampo( "nom_cgm" );
    $obListaMembroAdicional->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaMembroAdicional->commitDado();

    $obListaMembroAdicional->addDado();
    $obListaMembroAdicional->ultimoDado->setCampo( "adicional" );
    $obListaMembroAdicional->ultimoDado->setAlinhamento( 'CENTRO' );
    $obListaMembroAdicional->commitDado();

    $obListaMembroAdicional->montaHTML();
    $stHTML = $obListaMembroAdicional->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
           $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnMembroAdicional').innerHTML = '".$stHTML."';";

        return $stJs;
    } else {
        return $stHTML;
    }
}

function montaListaDocumento($arRecordSet , $boExecuta = true)
{
    if (!isset($stJs)) {
        $stJs="";
    }
    $rsListaDocumento = new RecordSet;

    $rsListaDocumento->preenche( $arRecordSet );
    $inCount = 0;

    $obListaDocumento = new Lista;
    $obListaDocumento->setTitulo('Documentos');
    $obListaDocumento->setMostraPaginacao( false );
    $obListaDocumento->setRecordSet( $rsListaDocumento );

    $obListaDocumento->addCabecalho();
    $obListaDocumento->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaDocumento->ultimoCabecalho->setWidth( 3 );
    $obListaDocumento->commitCabecalho();
    $obListaDocumento->addCabecalho();
    $obListaDocumento->ultimoCabecalho->addConteudo("Documento");
    $obListaDocumento->commitCabecalho();
    $obListaDocumento->addCabecalho();
    $obListaDocumento->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaDocumento->ultimoCabecalho->setWidth( 3 );
    $obListaDocumento->commitCabecalho();

    $obListaDocumento->addDado();
    $obListaDocumento->ultimoDado->setCampo( "nom_documento" );
    $obListaDocumento->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaDocumento->commitDado();

    $obListaDocumento->addAcao();
    $obListaDocumento->ultimaAcao->setAcao( "EXCLUIR" );
    $obListaDocumento->ultimaAcao->setFuncaoAjax( true );
    $obListaDocumento->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirDocumento');" );
    $obListaDocumento->ultimaAcao->addCampo("1","cod_documento");
    $obListaDocumento->ultimaAcao->addCampo("2","nom_documento");
    $obListaDocumento->commitAcao();

    $obListaDocumento->montaHTML();
    $stHTML = $obListaDocumento->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
           $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnDocumento').innerHTML = '".$stHTML."';";

        return $stJs;
    } else {
        return $stHTML;
    }
}

function montaListaDocumentoConsulta($arRecordSet , $boExecuta = true)
{
    if (!isset($stJs)) {
        $stJs="";
    }
    $rsListaDocumento = new RecordSet;
    $rsListaDocumento->preenche( $arRecordSet );
    $inCount = 0;

    $obListaDocumento = new Lista;
    $obListaDocumento->setTitulo('Documentos');
    $obListaDocumento->setMostraPaginacao( false );
    $obListaDocumento->setRecordSet( $rsListaDocumento );

    $obListaDocumento->addCabecalho();
    $obListaDocumento->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaDocumento->ultimoCabecalho->setWidth( 3 );
    $obListaDocumento->commitCabecalho();
    $obListaDocumento->addCabecalho();
    $obListaDocumento->ultimoCabecalho->addConteudo("Documento");
    $obListaDocumento->commitCabecalho();

    $obListaDocumento->addDado();
    $obListaDocumento->ultimoDado->setCampo( "nom_documento" );
    $obListaDocumento->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaDocumento->commitDado();

    $obListaDocumento->montaHTML();
    $stHTML = $obListaDocumento->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
           $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnDocumento').innerHTML = '".$stHTML."';";

        return $stJs;
    } else {
        return $stHTML;
    }
}

function montaSpanMembros($stAcao = "")
{
    if (!isset($stJs)) {
        $stJs="";
    }
    $rsLista = new RecordSet;
    $rsLista->preenche ( Sessao::read('arMembros') );

    $obLstMembros = new Lista;
    $obLstMembros->setTitulo('Membros');
    $obLstMembros->setMostraPaginacao( false );
    $obLstMembros->setRecordSet( $rsLista );

    $obLstMembros->addCabecalho();
    $obLstMembros->ultimoCabecalho->addConteudo("&nbsp;");
    $obLstMembros->ultimoCabecalho->setWidth( 3 );
    $obLstMembros->commitCabecalho();

    $obLstMembros->addCabecalho();
    $obLstMembros->ultimoCabecalho->addConteudo("CGM");
    $obLstMembros->ultimoCabecalho->setWidth( 3 );
    $obLstMembros->commitCabecalho();

    $obLstMembros->addCabecalho();
    $obLstMembros->ultimoCabecalho->addConteudo("Nome");
    $obLstMembros->ultimoCabecalho->setWidth( 40 );
    $obLstMembros->commitCabecalho();

    $obLstMembros->addCabecalho();
    $obLstMembros->ultimoCabecalho->addConteudo("Cargo");
    $obLstMembros->ultimoCabecalho->setWidth( 5 );
    $obLstMembros->commitCabecalho();

    $obLstMembros->addCabecalho();
    $obLstMembros->ultimoCabecalho->addConteudo("Comissão");
    $obLstMembros->ultimoCabecalho->setWidth( 20 );
    $obLstMembros->commitCabecalho();

    if ($stAcao != "consultar") {
        $obLstMembros->addCabecalho();
        $obLstMembros->ultimoCabecalho->addConteudo("&nbsp;");
        $obLstMembros->ultimoCabecalho->setWidth( 3 );
        $obLstMembros->commitCabecalho();
    }

    $obLstMembros->addDado();
    $obLstMembros->ultimoDado->setCampo( "numcgm" );
    $obLstMembros->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLstMembros->commitDado();

    $obLstMembros->addDado();
    $obLstMembros->ultimoDado->setCampo( "nom_cgm" );
    $obLstMembros->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLstMembros->commitDado();

    $obLstMembros->addDado();
    $obLstMembros->ultimoDado->setCampo( "tipo_membro" );
    $obLstMembros->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLstMembros->commitDado();

    $obLstMembros->addDado();
    $obLstMembros->ultimoDado->setCampo( "comissao" );
    $obLstMembros->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLstMembros->commitDado();

    if ($stAcao != "consultar") {
        $obLstMembros->addAcao();
        $obLstMembros->ultimaAcao->setAcao( "EXCLUIR" );
        $obLstMembros->ultimaAcao->setFuncaoAjax( true );
        $obLstMembros->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirMembro');" );
        $obLstMembros->ultimaAcao->addCampo("1","numcgm");
        $obLstMembros->commitAcao();
    }

    $obLstMembros->montaHTML();
    $stHTML = $obLstMembros->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnMembros').innerHTML = '".$stHTML."';";

    return $stJs;
}

function buscaParticipantes($inCodComissao, $stTipoComissao, $boFlag = false)
{
    if (!isset($stJs)) {
        $stJs="";
    }
    $arMembros = array();

    # Membros da Comissão da Licitação
    $arMembrosSessao = Sessao::read('arMembros');

    # Membros Adicionais
    $arMembrosAdicionais = Sessao::read('arMembro');

    if (is_array($arMembrosSessao)) {
        foreach ($arMembrosSessao as $registro) {
            if ($registro['comissao'] != $stTipoComissao) {
                $arMembros[] = $registro;
            }
        }
    }

    include_once(TLIC."TLicitacaoComissaoMembros.class.php");
    $obTLicitacaoComissaoMembros = new TLicitacaoComissaoMembros;

    if (!$boFlag)
        $obTLicitacaoComissaoMembros->recuperaMembrosPorComissao( $rsMembros, $inCodComissao );
    else
        $obTLicitacaoComissaoMembros->recuperaComissaoLicitacaoMembrosPorComissao( $rsMembros, $inCodComissao, $_REQUEST['inCodModalidade'], $_REQUEST['inCodLicitacao'] );

    $boExecuta = false;
    while (!$rsMembros->eof()) {
        if ($rsMembros->getCampo('cod_tipo_membro') == '2' OR  $rsMembros->getCampo('cod_tipo_membro') == '3') {
            $boExecuta = true;
        }
        $rsMembros->proximo();
    }

    if ($boExecuta == false) {
        Sessao::write('arMembros', array());
        Sessao::write('arMembro', array());

        $stJs .= "alertaAviso('Comissão de licitação não possui Presidente ou Pregoeiro.','form','erro','".Sessao::getId()."');";
        $stJs .= "jQuery('#inCodComissao').val('');  \n";

        echo montaSpanMembros();
        echo montaListaMembroAdicional(Sessao::read('arMembro'));
    } else {
        $stJs .= "jQuery('#inCodComissao').val('".$inCodComissao."');  \n";

    }

    $rsMembros->setPrimeiroElemento();

    if ($boExecuta) {
        if ($inCodComissao) {

            while (!$rsMembros->eof()) {

                $boCadastra = true;

                # Validação para retirar de Membro Adicional e colocar na Comissão de Licitação, se for apoio.
                if (is_array($arMembrosAdicionais)) {
                    foreach ($arMembrosAdicionais as $registro) {
                        if ($registro['num_cgm'] == $rsMembros->getCampo('numcgm')) {
                            # Guarda os ids dos Membros Adicionais que devem ser retirados da lista.
                            $arIdsMembrosAdicionais[] = $registro['num_cgm'];
                        }
                    }
                }

                if ($boCadastra) {
                    # Validação para não incluir participante que já esteja na lista de Comissão da Licitação.
                    if (is_array($arMembrosSessao)) {
                        foreach ($arMembrosSessao as $registro) {
                            if ($registro['numcgm'] == $rsMembros->getCampo('numcgm')) {
                                $boCadastra = false;
                                break;
                            } else
                                $boCadastra = true;
                        }
                    }
                }

                # Adiciona no array de Membros da Comissão o CGM.
                if ($boCadastra) {
                    $membro['comissao']        = $stTipoComissao;
                    $membro['cod_comissao']    = $rsMembros->getCampo('cod_comissao');
                    $membro['numcgm']          = $rsMembros->getCampo('numcgm');
                    $membro['nom_cgm']         = $rsMembros->getCampo('nom_cgm');
                    $membro['tipo_membro']     = $rsMembros->getCampo('tipo_membro');
                    $membro['cod_tipo_membro'] = $rsMembros->getCampo('cod_tipo_membro');
                    $membro['cod_norma']       = $rsMembros->getCampo('cod_norma');

                    $arMembros[] = $membro;
                }
                $rsMembros->proximo();
            }
        }

        if (isset($arIdsMembrosAdicionais)&&is_array($arIdsMembrosAdicionais)) {

            # Remove a duplicidade de Ids.
            $arIdsMembrosAdicionais = array_unique($arIdsMembrosAdicionais);

            # Monta as requisições para exclusão dos Membros.
            foreach ($arIdsMembrosAdicionais as $keyData) {
                $stJs .= "removeMembroAdicional(".$keyData."); ";
            }
        }
        Sessao::write('arMembros', $arMembros);
        $stJs .= montaSpanMembros();
    }
    echo $stJs;
}

function montaListaItens($rsItens)
{
    // formata recordset
    $rsItens->setPrimeiroElemento();

    $rsItens->addFormatacao('valor_unitario'      , 'NUMERIC_BR');
    $rsItens->addFormatacao('quantidade'          , 'NUMERIC_BR_4');
    $rsItens->addFormatacao('quantidade_real'     , 'NUMERIC_BR_4');
    $rsItens->addFormatacao('valor_total_real'    , 'NUMERIC_BR');
    $rsItens->addFormatacao('valor_ultima_compra' , 'NUMERIC_BR');

    require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

    $table = new Table();

    $table->setRecordset( $rsItens );
    $table->setSummary('Itens');

    $table->Head->addCabecalho( 'Item'                   , 30);
    $table->Head->addCabecalho( 'Centro de Custo'        , 25);
    $table->Head->addCabecalho( 'Valor de Referência'    , 10);
    $table->Head->addCabecalho( 'Valor da Última Compra' , 10);
    $table->Head->addCabecalho( 'Qtde'                   , 10);
    $table->Head->addCabecalho( 'Valor Total'            , 10);

    $table->Body->addCampo( '[cod_item] - [descricao_completa]. [complemento]' , 'E');
    $table->Body->addCampo( '[cod_centro] - [centro_custo_descricao]' , 'E');
    $table->Body->addCampo( 'valor_unitario' , 'D');
    $table->Body->addCampo( 'valor_ultima_compra' , 'D');
    $table->Body->addCampo( 'quantidade_real' , 'D' );
    $table->Body->addCampo( 'valor_total_real' , 'D');

    $table->montaHTML();
    $stHTML = $table->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);

    $stJs = "d.getElementById('spnItens').innerHTML = '" . $stHTML . "';";

    return $stJs;
}

function montaComissao($stDataVigencia = ""){
    include TLIC."TLicitacaoComissao.class.php";
    $obTLicitacaoComissao = new TLicitacaoComissao();

    Sessao::write('arMembro', array());
    Sessao::write('arMembros', array());

    $stJs  = "jQuery('#spnMembros').val(''); \n";
    $stJs .= montaSpanMembros();
    $stJs .= montaListaMembroAdicional(Sessao::read('arMembro'));
    $stJs .= "jQuery('#inCodComissao').empty().append(new Option('Selecione','') ); \n";

    $stFiltroVigencia = "   AND ".Sessao::getExercicio()." BETWEEN to_char(norma.dt_publicacao,'yyyy')::INTEGER AND to_char(norma_data_termino.dt_termino,'yyyy')::INTEGER \n";

    if (!empty($stDataVigencia))
        $stFiltroVigencia = "   AND to_date('".$stDataVigencia."','dd/mm/yyyy') BETWEEN norma.dt_publicacao AND norma_data_termino.dt_termino \n";

    $stFiltro  = " WHERE comissao.cod_tipo_comissao <> 4 \n";
    $stFiltro .= "   AND comissao.ativo = true \n";
    $stFiltro .= $stFiltroVigencia;
    $obTLicitacaoComissao->recuperaComissoesCombo( $rsRecordSetComissao,$stFiltro,' ORDER BY comissao.cod_comissao');

    foreach ($rsRecordSetComissao->getElementos() as $option) {
        $stJs .= "jQuery('#inCodComissao').prop('disabled',false); \n";
        $stOption = $option['finalidade']." ( Vigência: ".$option['dt_publicacao']." ".$option['dt_termino'];
        $stJs .= "jQuery('#inCodComissao').append(new Option(\"".$stOption."\", \"".$option['cod_comissao']."\") ); \n";
    }

    return $stJs;
}

switch ($stCtrl) {

    case 'valorMinMax':
        if(!$boEdital) {
            $obMaxMin = new Moeda();
        } else {
            $obMaxMin = new Label();
        }

        if ($_REQUEST['inCodModalidade'] == 4) {
            $obMaxMin->setName('stValor');
            $obMaxMin->setRotulo('Valor Mínimo');
        } else {
            $obMaxMin->setName('stValor');
            $obMaxMin->setRotulo('Valor Máximo');
        }

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obMaxMin);
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
        $stJs .= "d.getElementById('spnMaxMin').innerHTML = '".$stHTML."';\n";

        include_once(TLIC."TLicitacaoModalidadeDocumentos.class.php");
        $obTLicitacaoModalidadeDocumentos = new TLicitacaoModalidadeDocumentos();
        $obTLicitacaoModalidadeDocumentos->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);

        if ($_REQUEST['inCodModalidade'] != "")
            $obTLicitacaoModalidadeDocumentos->recuperaDocumentos($rsDocumentos);

        if (Sessao::read('arDocumentos')) {
             Sessao::write('arDocumentosAux', Sessao::read('arDocumentos'));
             Sessao::write('arDocumentos', "");
             $boSessao = true;
        }

        $inCount = 0;
        if ($rsDocumentos) {
            while (!$rsDocumentos->eof()) {
                $arDocumentos[$inCount]['nom_documento'] = $rsDocumentos->getCampo('nom_documento');
                $arDocumentos[$inCount]['cod_documento'] = $rsDocumentos->getCampo('cod_documento');
                $arDocumentos[$inCount]['modalidade']    = 'true';
                $inCount++;
                $rsDocumentos->proximo();
            }
            Sessao::write('arDocumentos', $arDocumentos);
        }

        if ($boSessao) {
            foreach (Sessao::read('arDocumentosAux') as $key =>  $value) {
                if ($value['modalidade'] == 'false') {
                    $arDocumentos[$inCount]['nom_documento'] = $value['nom_documento'];
                    $arDocumentos[$inCount]['cod_documento'] = $value['cod_documento'];
                    $arDocumentos[$inCount]['modalidade']    = $value['modalidade'];
                    $inCount++;
                }
            }
            Sessao::write('arDocumentos', $arDocumentos);
        }

        if (Sessao::read('arDocumentos') != null)

        $stJs .= montaListaDocumento(Sessao::read('arDocumentos'));

    break;

    case 'incluirMembroAdicional':
        $boErro = false;
        $arrayMembro  = Sessao::read('arMembro');
        $arrayMembros = Sessao::read('arMembros');

        # Valida se o usuário informado já não está na lista de Membros Adicionais.
        if (count($arrayMembro) > 0 )
            foreach ($arrayMembro as $arMembro)
                if ($_REQUEST['inCGM'] == $arMembro['num_cgm']) {
                    $boErro = true;
                    $stErro = "O membro escolhido já está na lista de Membro Adicional";
                    $stErroCgm = " (".$_REQUEST['inCGM']." - ".$_REQUEST['stNomCGM'].")";
                    $stErro .= $stErroCgm;
                }

        # Valida se o usuário informado já não está na lista de Membros da Comissão.
        if (count($arrayMembros) > 0 ) {
            foreach ($arrayMembros as $arMembro)
                if ($_REQUEST['inCGM'] == $arMembro['numcgm']) {
                    $boErro = true;
                    $stErro = "O membro escolhido já está na lista de Membro da Comissão de Licitação";
                    $stErroCgm = " (".$_REQUEST['inCGM']." - ".$_REQUEST['stNomCGM'].")";
                    $stErro .= $stErroCgm;
                }
        }
        # Validação para que só possa inserir membros adicionais se já tiver Comissão da Licitação.
        else if (count($arrayMembros) == 0) {
            $boErro = true;
            $stErro = "Você deve selecionar uma Comissão de Licitação.";
        }

        if (!$boErro) {

            # Validação para saber se o Membro Adicional incluso deve ir para a lista de Comissão ou Membro Adicional.
            include_once(TLIC."TLicitacaoComissaoMembros.class.php");
            $obTLicitacaoComissaoMembros = new TLicitacaoComissaoMembros;

            if ($_REQUEST['inCodComissao'])
                $obTLicitacaoComissaoMembros->recuperaMembrosPorComissao($rsMembros , $_REQUEST['inCodComissao']);

            $boFlag = false;

            # Verifica se o Membro Adicional não faz parte da Comissão Permanente.
            while (!$rsMembros->eof()) {

                if ($_REQUEST['inCGM'] == $rsMembros->getCampo('numcgm')) {
                    $boFlag = true;
                    $inCountMembros = sizeof($arrayMembros);

                    $arrayMembros[$inCountMembros]['comissao']        = 'Comissão Permanente';
                    $arrayMembros[$inCountMembros]['cod_comissao']    = $rsMembros->getCampo('cod_comissao');
                    $arrayMembros[$inCountMembros]['numcgm']          = $rsMembros->getCampo('numcgm');
                    $arrayMembros[$inCountMembros]['nom_cgm']         = $rsMembros->getCampo('nom_cgm');
                    $arrayMembros[$inCountMembros]['tipo_membro']     = $rsMembros->getCampo('tipo_membro');
                    $arrayMembros[$inCountMembros]['cod_tipo_membro'] = $rsMembros->getCampo('cod_tipo_membro');
                    $arrayMembros[$inCountMembros]['cod_norma']       = $rsMembros->getCampo('cod_norma');
                }
                $rsMembros->proximo();
            }

            # Verifica se o Membro Adicional não faz parte da Comissão de Apoio.
            if ($_REQUEST['inCodComissaoApoio'] && !$boFlag) {
                $obTLicitacaoComissaoMembros->recuperaMembrosPorComissao($rsMembrosApoio , $_REQUEST['inCodComissaoApoio']);

                while (!$rsMembrosApoio->eof()) {

                    if ($_REQUEST['inCGM'] == $rsMembrosApoio->getCampo('numcgm')) {
                        $boFlag = true;
                        $inCountMembros = sizeof($arrayMembros);

                        $arrayMembros[$inCountMembros]['comissao']        = 'Comissão de Apoio';
                        $arrayMembros[$inCountMembros]['cod_comissao']    = $rsMembrosApoio->getCampo('cod_comissao');
                        $arrayMembros[$inCountMembros]['numcgm']          = $rsMembrosApoio->getCampo('numcgm');
                        $arrayMembros[$inCountMembros]['nom_cgm']         = $rsMembrosApoio->getCampo('nom_cgm');
                        $arrayMembros[$inCountMembros]['tipo_membro']     = $rsMembrosApoio->getCampo('tipo_membro');
                        $arrayMembros[$inCountMembros]['cod_tipo_membro'] = $rsMembrosApoio->getCampo('cod_tipo_membro');
                        $arrayMembros[$inCountMembros]['cod_norma']       = $rsMembrosApoio->getCampo('cod_norma');
                    }
                    $rsMembrosApoio->proximo();
                }
            }

            if ($boFlag) {
                Sessao::write("arMembros", $arrayMembros);
                $stJs .= montaSpanMembros($arrayMembros);
            } else {
                $inCount = sizeof($arrayMembro);

                $arrayMembro[$inCount]['num_cgm']            = $_REQUEST['inCGM'];
                $arrayMembro[$inCount]['nom_cgm']            = $_REQUEST['stNomCGM'];
                $arrayMembro[$inCount]['cargo_membro']       = $_REQUEST['stCargoMembro'];
                $arrayMembro[$inCount]['cod_natureza_cargo'] = $_REQUEST['inCodNaturezaCargoMembro'];
                $arrayMembro[$inCount]['adicional']          = 'Sim';

                Sessao::write("arMembro", $arrayMembro);

                $stJs .= montaListaMembroAdicional($arrayMembro);
            }

        } else {
            $stJs .= "alertaAviso( '$stErro','form','erro','".Sessao::getId()."' );";
        }

    break;

    case 'excluirMembroAdicional':
        $arMembro = array();
        $inCount = 0;
        $key = trim($_REQUEST['num_cgm']);

        $arMembroExcluir = Sessao::read('arMembro');

        if ($arMembroExcluir) {
            foreach ($arMembroExcluir as $value) {
                $keyValue = trim($value['num_cgm']);
                if ($key != $keyValue) {
                    $arMembro[$inCount]['nom_cgm']              = $value['nom_cgm'];
                    $arMembro[$inCount]['num_cgm']              = $value['num_cgm'];
                    $arMembro[$inCount]['cargo_membro']         = $value['cargo_membro'];
                    $arMembro[$inCount]['cod_natureza_cargo']   = $value['cod_natureza_cargo'];
                    $arMembro[$inCount]['adicional']            = $value['adicional'];
                    $inCount++;
                }
            }
        }

        Sessao::write('arMembro', array());
        Sessao::write('arMembro', $arMembro);

        $stJs .= "limpaFormularioMembroAdicional();". montaListaMembroAdicional($arMembro);
    break;

    case 'excluirMembro':

        $arAux     = array();
        $inCount   = $inCountApoio = 0;
        $key       = trim($_REQUEST['numcgm']);
        $arMembros = Sessao::read('arMembros');

        foreach ($arMembros as $value) {
            $keyValue = trim($value['numcgm']);

            if ($key != $keyValue) {

                # Verifica se existe algum membro da comissão de apoio.
                if ($value['comissao'] == 'Comissão de Apoio')
                    $inCountApoio++;

                $arAux[$inCount]['comissao']        = $value['comissao'];
                $arAux[$inCount]['numcgm']          = $value['numcgm'];
                $arAux[$inCount]['nom_cgm']         = $value['nom_cgm'];
                $arAux[$inCount]['tipo_membro']     = $value['tipo_membro'];
                $arAux[$inCount]['cod_tipo_membro'] = $value['cod_tipo_membro'];
                $arAux[$inCount]['cod_comissao']    = $value['cod_comissao'];
                $arAux[$inCount]['cod_norma']       = $value['cod_norma'];
                $inCount++;
            }
            # Não permite excluir o membro Presidente da Comissão.
            else if ($value['cod_tipo_membro'] == 2) {
                $arAux[$inCount]['comissao']        = $value['comissao'];
                $arAux[$inCount]['numcgm']          = $value['numcgm'];
                $arAux[$inCount]['nom_cgm']         = $value['nom_cgm'];
                $arAux[$inCount]['tipo_membro']     = $value['tipo_membro'];
                $arAux[$inCount]['cod_tipo_membro'] = $value['cod_tipo_membro'];
                $arAux[$inCount]['cod_comissao']    = $value['cod_comissao'];
                $arAux[$inCount]['cod_norma']       = $value['cod_norma'];
                $stJs .= "alertaAviso( 'Não é possível excluir o membro presidente da comissão.','form','erro','".Sessao::getId()."' );";
                $inCount++;
            } elseif ($value['cod_tipo_membro'] == 3) {
                $arAux[$inCount]['comissao']        = $value['comissao'];
                $arAux[$inCount]['numcgm']          = $value['numcgm'];
                $arAux[$inCount]['nom_cgm']         = $value['nom_cgm'];
                $arAux[$inCount]['tipo_membro']     = $value['tipo_membro'];
                $arAux[$inCount]['cod_tipo_membro'] = $value['cod_tipo_membro'];
                $arAux[$inCount]['cod_comissao']    = $value['cod_comissao'];
                $arAux[$inCount]['cod_norma']       = $value['cod_norma'];
                $stJs .= "alertaAviso( 'Não é possível excluir o membro pregoeiro da comissão.','form','erro','".Sessao::getId()."' );";
                $inCount++;
            }
        }

        # Se não existir nenhum membro da comissão de apoio, limpa o objeto select do form.
        if ($inCountApoio == 0)
            $stJs .= "jQuery('#inCodComissaoApoio').selectOptions('', true); ";

        $arMembros = array();
        $arMembros = $arAux;

        # Limpa o array da sessão para futura atualização.
        Sessao::write('arMembros', $arMembros);

        $stJs .= montaSpanMembros($arMembros);
    break;

    case 'excluirDocumento':

        $arDocumento = array();
        $inCount = 0;

        $key = trim($_REQUEST['cod_documento']."-".$_REQUEST['nom_documento']);

        $arDocumentosExcluir = Sessao::read('arDocumentos');
        $arDocumentoExcluidos = Sessao::read('arDocumentosExcluidos');

        foreach ($arDocumentosExcluir as $value) {
            $keyValue = trim($value['cod_documento']."-".$value['nom_documento']);
            if ($key != $keyValue) {
                $arDocumento[$inCount]['cod_documento'  ] = $value['cod_documento'  ];
                $arDocumento[$inCount]['nom_documento'] = $value['nom_documento'];
                $arDocumento[$inCount]['modalidade'] = $value['modalidade'];
                $inCount++;
            } else {
                $inCountExcluidos = sizeof($arDocumentoExcluidos);
                $arDocumentoExcluidos[$inCountExcluidos]['cod_documento'  ] = $value['cod_documento'  ];
                $arDocumentoExcluidos[$inCountExcluidos]['nom_documento'] = $value['nom_documento'];
                $arDocumentoExcluidos[$inCountExcluidos]['modalidade'] = $value['modalidade'];
            }
        }

        Sessao::remove('arDocumentos');
        Sessao::remove('arDocumentosExcluidos');
        $inQuantExcluidos = Sessao::read('inQuantidadeDocumentosExcluidos');

        $inQuantExcluidos++;

        Sessao::write('inQuantidadeDocumentosExcluidos',$inQuantExcluidos);
        Sessao::write('arDocumentos', $arDocumento);
        Sessao::write('arDocumentosExcluidos', $arDocumentoExcluidos);

        $stJs .= montaListaDocumento( $arDocumento );

    break;
    case 'comissaoMembro'     :
    case 'comissaoMembroApoio':

            # Tentei comentar ao máximo essa peripécia que fiz manutenção.
            $arMembros      = array();
            $stAcao         = $request->get('stAcao');
            $inCodComissao  = $_REQUEST['inCodComissao'] ? $_REQUEST['inCodComissao'] : $_REQUEST['inCodComissaoApoio'];
            $stTipoComissao = ($stCtrl == 'comissaoMembroApoio') ? 'Comissão de Apoio' : 'Comissão Permanente';

            # Caso haja uma comissão seleciona, busca os participantes da mesma.
            if (!empty($inCodComissao)) {
                $stJs .= "habilitaEquipeApoio(true); ";
                $stJs .= buscaParticipantes($inCodComissao, $stTipoComissao);

                echo montaSpanMembros($stAcao);
            }
            # Caso seja desfeita a escolha da comissão da licitação, limpa todos os membros participantes.
            else if ($stCtrl == 'comissaoMembro') {
                $stJs .= "habilitaEquipeApoio(false);";

                # Limpa os registros nos arrays de Membros (Comissão da licitação).
                Sessao::write('arMembros', array());
                # Limpa os registros nos arrays de Membros (Membros Adicionais).
                Sessao::write('arMembro', array());

                echo montaSpanMembros();
                echo montaListaMembroAdicional(Sessao::read('arMembro'));
            }
            # Caso seja desfeita a escolha da comissão de apoio, atualiza a comissão principal sem os participantes do apoio.
            else if ($stCtrl == 'comissaoMembroApoio') {
                $arrAux = array();
                foreach (Sessao::read('arMembros') as $key => $value) {
                    if ($value['comissao'] != "Comissão de Apoio") {
                        $stMembro['comissao']        = $value['comissao'];
                        $stMembro['cod_comissao']    = $value['cod_comissao'];
                        $stMembro['numcgm']          = $value['numcgm'];
                        $stMembro['nom_cgm']         = $value['nom_cgm'];
                        $stMembro['tipo_membro']     = $value['tipo_membro'];
                        $stMembro['cod_tipo_membro'] = $value['cod_tipo_membro'];
                        $stMembro['cod_norma']       = $value['cod_norma'];

                        $arrAux[] = $stMembro;
                    }
                }

                Sessao::write('arMembros', $arrAux);
                echo montaSpanMembros();
            }

    break;

    case 'consultaComissaoMembros':

        include_once(TCOM."TComprasMapaItem.class.php");
        include_once(TLIC."TLicitacaoComissaoMembros.class.php");
        include_once(TLIC."TLicitacaoComissaoLicitacao.class.php");

        $stAcao          = $_REQUEST['stAcao'];
        $inCodModalidade = $_REQUEST['inCodModalidade'];
        $inCodEntidade   = $_REQUEST['inCodEntidade'];
        $inCodLicitacao  = $_REQUEST['inCodLicitacao'];
        $stExercicio     = $_REQUEST['stExercicio'];

        $obTLicitacaoComissaoLicitacao = new TLicitacaoComissaoLicitacao;
        $obTLicitacaoComissaoLicitacao->setDado('cod_licitacao'  , $inCodLicitacao  );
        $obTLicitacaoComissaoLicitacao->setDado('cod_modalidade' , $inCodModalidade );
        $obTLicitacaoComissaoLicitacao->setDado('cod_entidade'   , $inCodEntidade   );
        $obTLicitacaoComissaoLicitacao->setDado('exercicio'      , $stExercicio     );
        $obTLicitacaoComissaoLicitacao->recuperaComissaoLicitacao($rsComissaoLicitacao);

        $obTLicitacaoComissaoMembros = new TLicitacaoComissaoMembros;

        $inCount = 0;

        while (!$rsComissaoLicitacao->eof()) {
            if ($rsComissaoLicitacao->getCampo('equipe') == 'apoio') {
                buscaParticipantes( $rsComissaoLicitacao->getCampo('cod_comissao') ,'Comissão de Apoio' ,true );
            } else {
                buscaParticipantes( $rsComissaoLicitacao->getCampo('cod_comissao')  , 'Comissão Permanente',true) ;
            }
            $rsComissaoLicitacao->proximo();
        }

        $stJs .= montaSpanMembros("consultar");
    break;

    case 'vlTotalReferencia':
        $stJs = '';
        
        if (!empty($_REQUEST['mapaCompras'])) {
            $rsRecordSet     = new RecordSet();
            $mapaCompras = explode("/",$_REQUEST['mapaCompras']);
            $inCodMapa       = $mapaCompras[0];
            $stExercicioMapa = $mapaCompras[1];
            
            if (empty($stExercicioMapa)) {
                $stExercicioMapa = Sessao::getExercicio();
            }
            
            $boLimpa = true;

            $stFiltro  = " AND mapa.cod_mapa  =  ".$inCodMapa."
                           AND mapa.exercicio = '".$stExercicioMapa."'
                           AND NOT EXISTS(SELECT cotacao.cod_cotacao
                                               , cotacao.exercicio
                                               , max(cotacao.timestamp) as timestamp
                                            FROM compras.cotacao
                                      INNER JOIN empenho.item_pre_empenho_julgamento
                                              ON item_pre_empenho_julgamento.cod_cotacao = cotacao.cod_cotacao
                                             AND item_pre_empenho_julgamento.exercicio   = cotacao.exercicio
                                      INNER JOIN empenho.item_pre_empenho                                                       
                                              ON item_pre_empenho.cod_pre_empenho = item_pre_empenho_julgamento.cod_pre_empenho
                                             AND item_pre_empenho.exercicio       = item_pre_empenho_julgamento.exercicio
                                             AND item_pre_empenho.num_item        = item_pre_empenho_julgamento.num_item
                                      INNER JOIN empenho.pre_empenho
                                              ON item_pre_empenho.exercicio = pre_empenho.exercicio
                                             AND item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                      INNER JOIN empenho.autorizacao_empenho
                                              ON autorizacao_empenho.exercicio       = pre_empenho.exercicio
                                             AND autorizacao_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                      INNER JOIN compras.mapa_cotacao
                                              ON mapa_cotacao.exercicio_cotacao = cotacao.exercicio
                                             AND mapa_cotacao.cod_cotacao       = cotacao.cod_cotacao
                                           WHERE mapa_cotacao.exercicio_mapa = mapa.exercicio
                                             AND mapa_cotacao.cod_mapa       = mapa.cod_mapa
                                        GROUP BY cotacao.exercicio                                                                         
                                               , cotacao.cod_cotacao )";

            if ( $_REQUEST['stAcao'] == 'alterar' ){
                $arModalidade = explode('-',$_REQUEST['stModalidade']);
                $inCodModalidade = trim($arModalidade[0]);
                $inHdnCodLicitacao = $_REQUEST['inCodLicitacao'];
                $stHdnExercicioLicitacao = $_REQUEST['stExercicioLicitacao'];
                
                $inCodLicitacaoMapa = SistemaLegado::pegaDado('cod_licitacao','licitacao.licitacao'," WHERE exercicio_mapa = '".$stExercicioMapa."' AND cod_mapa = ".$inCodMapa);
                $stExercicioLicitacaoMapa = SistemaLegado::pegaDado('exercicio','licitacao.licitacao'," WHERE exercicio_mapa = '".$stExercicioMapa."' AND cod_mapa = ".$inCodMapa);
            
                if ($_REQUEST['inCodModalidade'] == 3 || $_REQUEST['inCodModalidade'] == 6 || $_REQUEST['inCodModalidade'] == 7 ) {
                    $stComplementoFiltro .= " \n";
                } else {
                    $stComplementoFiltro .= "
                        AND EXISTS(SELECT mp.exercicio
                                        , mp.cod_mapa
                                        , mp.cod_objeto
                                        , mp.timestamp
                                        , mp.cod_tipo_licitacao
                                        , solicitacao.registro_precos
                                     FROM compras.mapa AS mp
                               INNER JOIN compras.mapa_solicitacao
                                       ON mapa_solicitacao.exercicio = mp.exercicio
                                      AND mapa_solicitacao.cod_mapa  = mp.cod_mapa
                               INNER JOIN compras.solicitacao_homologada
                                       ON solicitacao_homologada.exercicio       = mapa_solicitacao.exercicio_solicitacao
                                      AND solicitacao_homologada.cod_entidade    = mapa_solicitacao.cod_entidade
                                      AND solicitacao_homologada.cod_solicitacao = mapa_solicitacao.cod_solicitacao
                               INNER JOIN compras.solicitacao
                                       ON solicitacao.exercicio       = solicitacao_homologada.exercicio
                                      AND solicitacao.cod_entidade    = solicitacao_homologada.cod_entidade
                                      AND solicitacao.cod_solicitacao = solicitacao_homologada.cod_solicitacao
                                    WHERE mp.cod_mapa = mapa.cod_mapa
                                      AND mp.exercicio = mapa.exercicio
                                      AND solicitacao.registro_precos IS FALSE
                                 GROUP BY mp.exercicio
                                        , mp.cod_mapa
                                        , mp.cod_objeto
                                        , mp.timestamp
                                        , mp.cod_tipo_licitacao
                                        , solicitacao.registro_precos )
                    \n";
                }
            }
            $obTComprasMapa = new TComprasMapa();
            $obTComprasMapa->recuperaMapaSemReservaProcessoLicitatorio($rsRecordSet, $stFiltro.$stComplementoFiltro);

            if ($rsRecordSet->getNumLinhas() > 0 || ($_REQUEST['stAcao'] == 'alterar' AND $inCodLicitacaoMapa == $inHdnCodLicitacao AND $stExercicioLicitacaoMapa == $stHdnExercicioLicitacao)) {
                if (empty($stExercicioMapa)) {
                    $stExercicioMapa = Sessao::getExercicio();
                }
                $stFiltro  = " AND mapa.cod_mapa  = ".$inCodMapa;
                $stFiltro .= " AND mapa.exercicio = '".$stExercicioMapa."'";
                $obTComprasMapa->recuperaMapaSemReservaProcessoLicitatorio($rsMapa, $stFiltro );

                if ($rsMapa->getNumLinhas() > 0 || ($_REQUEST['stAcao'] == 'alterar' AND $inCodLicitacaoMapa == $inHdnCodLicitacao AND $stExercicioLicitacaoMapa == $stHdnExercicioLicitacao)) {
                    $obTComprasMapa->setDado('cod_mapa'      , $inCodMapa);
                    $obTComprasMapa->setDado('exercicio_mapa', $stExercicioMapa);
                    $obTComprasMapa->recuperaMapaCotacaoValida($rsCotacao);
                    if ($rsCotacao->getNumLinhas() > 0) {
                        include_once( CAM_GP_COM_MAPEAMENTO."TComprasJulgamento.class.php");
                        $obTComprasJulgamento = new TComprasJulgamento();
                        $stFiltro  = " WHERE julgamento.exercicio   = '".$rsCotacao->getCampo('exercicio_cotacao')."'";
                        $stFiltro .= "   AND julgamento.cod_cotacao = ". $rsCotacao->getCampo('cod_cotacao');
                        $obTComprasJulgamento->recuperaJulgamentoAutorizacao($rsAutorizacao, $stFiltro);
                        if ($rsAutorizacao->getNumLinhas() > 0) {
                            $boExecuta = false;
                        } else {
                            $boExecuta = true;
                        }
                    } else {
                        $boExecuta = true;
                    }

                    if ($boExecuta) {
                        $obTComprasMapaItem = new TComprasMapaItem();
                        $obTComprasMapaItem->setDado('cod_mapa' , $inCodMapa);
                        $obTComprasMapaItem->setDado('exercicio', $stExercicioMapa);
                        $obTComprasMapaItem->recuperaValorTotal($rsVlTotal);

                        $intTotalReferencial = $rsVlTotal->getCampo('vl_total');
                        $rsVlTotal->addFormatacao('vl_total','NUMERIC_BR');
                        //// buscando o tipo de cotação

                        if ($rsVlTotal->getNumLinhas() > 0) {
                            $stJs .= "d.getElementById('stValorReferencia').innerHTML = '". $rsVlTotal->getCampo('vl_total') ."';\n";
                            $stJs .= "d.getElementById('stValorReferencial').value = '".$intTotalReferencial."';\n";

                            # Buscando o objeto
                            $obTComprasMapa->setDado('cod_mapa' , $inCodMapa);
                            $obTComprasMapa->setDado('exercicio', $stExercicioMapa);
                            $obTComprasMapa->recuperaMapaObjeto($rsMapa);

                            $boLimpa = false;
                            $txtObjeto = $rsMapa->getCampo('descricao');
                            $txtObjeto = nl2br(addslashes(str_replace("\r\n", "\n", preg_replace("/(\r\n|\n|\r)/", "", $txtObjeto))));
                            $stJs .= "d.getElementById('txtObjeto').innerHTML = '$txtObjeto';\n";
                            $stJs .= "d.getElementById('stObjeto' ).value = '".$rsMapa->getCampo('cod_objeto')."';\n";
                            $txtTipoCotacao = $rsMapa->getCampo('cod_tipo_licitacao').' - '.$rsMapa->getCampo('tipo_licitacao');
                            $stJs .= "d.getElementById('stTipoCotacao').innerHTML = '".$txtTipoCotacao."';\n";
                            $stJs .= "f.inCodTipoCotacao.value = '".$rsMapa->getCampo('cod_tipo_licitacao')."';\n";
                            
                            $obTComprasMapa->recuperaTipoMapa($rsTipoMapa);
                            if ($rsTipoMapa->getCampo('registro_precos') == 't') {
                                $txtRegistroPrecos = 'Sim';
                                $inTipoRegistroPrecos = 1;
                                $stCodModalidade = 'IN (3,6,7)';
                            } else {
                                $txtRegistroPrecos = 'Não';
                                $inTipoRegistroPrecos = 0;
                                $stCodModalidade = 'NOT IN (4,5,10,11)';
                            }

                            $stJs .= "d.getElementById('txtTipoRegistroPrecos').innerHTML = '$txtRegistroPrecos';\n";
                            $stJs .= "d.getElementById('boHdnTipoRegistroPrecos').value = '$inTipoRegistroPrecos';\n";

                            include_once(CAM_GP_COM_MAPEAMENTO."TComprasModalidade.class.php");
                            $obComprasModalidade = new TComprasModalidade();
                            $rsModalidade = new RecordSet();
                            $stFiltro = " WHERE	cod_modalidade ".$stCodModalidade." ";
                        
                            $obComprasModalidade->recuperaTodos($rsModalidade,$stFiltro,"ORDER BY cod_modalidade",$boTransacao);
                            
                            $stJs .= "jQuery('#inCodModalidade').removeOption(/./); \n";
                            $stJs .= "jQuery('#inCodModalidade').addOption('','Selecione'); \n";
                            while(!$rsModalidade->eof())
                            {
                                $stJs .= "jQuery('#inCodModalidade').addOption('".$rsModalidade->getCampo('cod_modalidade')."','".$rsModalidade->getCampo('cod_modalidade').' - '.$rsModalidade->getCampo('descricao')."'); \n";
                                $rsModalidade->proximo();
                            }
                            
                            $stJs .= "jQuery('#inCodModalidade').val(''); \n";
                        }
                    } else {
                        $stJs .= "alertaAviso('Mapa de compras '".$_REQUEST['mapaCompras']."' já está em processo licitatório.','form','erro','".Sessao::getId()."');";
                    }
                }
            } else {
                include_once CAM_GP_COM_MAPEAMENTO."TComprasCompraDireta.class.php";
                $obTComprasCompraDireta = new TComprasCompraDireta();
                $stFiltro  = " WHERE compra_direta.exercicio_mapa = '".$stExercicioMapa."'";
                $stFiltro .= "   AND compra_direta.cod_mapa       =  ".$inCodMapa;
                $obTComprasCompraDireta->recuperaMapaCompraDireta($rsRecordSet, $stFiltro);
                if ($rsRecordSet->getNumLinhas() > 0) {
                    $boLimpa = true;
                    $stJs .= "alertaAviso('Mapa de compras (".$_REQUEST['mapaCompras'].") já está sendo utilizado pela compra direta.','form','erro','".Sessao::getId()."');\n";
                } else {
                    $boLimpa = false;
                }

                if (!$boLimpa) {
                    include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacao.class.php");
                    $obTLicitacaoLicitacao = new TLicitacaoLicitacao();
                    $obTLicitacaoLicitacao->setDado('cod_mapa'       , $inCodMapa);
                    $obTLicitacaoLicitacao->setDado('exercicio_mapa' , $stExercicioMapa);
                    $obTLicitacaoLicitacao->recuperaLicitacao($rsRecordSet);

                    if ($rsRecordSet->getNumLinhas() > 0) {
                        $boLimpa = true;
                        $stJs .= "alertaAviso('Mapa de compras (".$_REQUEST['mapaCompras'].") já está sendo utilizado pela licitação.','form','erro','".Sessao::getId()."');\n";
                    } else {
                        $boLimpa = false;
                    }
                }

                if (!$boLimpa) {
                    $obTComprasMapa->setDado('cod_mapa' , $inCodMapa);
                    $obTComprasMapa->setDado('exercicio', $stExercicioMapa);
                    $obTComprasMapa->recuperaTipoMapa($rsRecordSet);

                    if ($rsRecordSet->getNumLinhas() > 0 && $rsRecordSet->getCampo('registro_precos') == 't' ) {
                        $boLimpa = true;
                        $stJs .= "alertaAviso('Mapa de compras (".$_REQUEST['mapaCompras']."). A Modalidade desse Processo Licitatório, não é compativel com o mapa de Registro de Preços.','form','erro','".Sessao::getId()."');\n";
                    }
                }                
            }

            if ($boLimpa) {
                $stJs .= "jQuery('#stValorReferencia').html('&nbsp;'); \n";
                $stJs .= "jQuery('#stValorReferencial').val('');       \n";
                $stJs .= "jQuery('#stTipoCotacao').html('&nbsp;');     \n";
                $stJs .= "jQuery('#stTipoCotacao').val('');            \n";
                $stJs .= "jQuery('#stObjeto').val('');                 \n";
                $stJs .= "jQuery('#txtObjeto').html('&nbsp;');         \n";
                $stJs .= "jQuery('#txtTipoRegistroPrecos').html('&nbsp;'); \n";
                $stJs .= "jQuery('#boHdnTipoRegistroPrecos').val(''); \n";
                $stJs .= "jQuery('#inCodModalidade').removeOption(/./); \n";
                $stJs .= "jQuery('#inCodModalidade').addOption('','Selecione');\n";
            }
        } else {
            $stJs .= "jQuery('#stValorReferencia').html('&nbsp;'); \n";
            $stJs .= "jQuery('#stValorReferencial').val('');       \n";
            $stJs .= "jQuery('#stTipoCotacao').html('&nbsp;');     \n";
            $stJs .= "jQuery('#stTipoCotacao').val('');            \n";
            $stJs .= "jQuery('#stObjeto').val('');                 \n";
            $stJs .= "jQuery('#txtObjeto').html('&nbsp;');         \n";
            $stJs .= "jQuery('#txtTipoRegistroPrecos').html('&nbsp;'); \n";
            $stJs .= "jQuery('#boHdnTipoRegistroPrecos' ).val(''); \n";
            $stJs .= "jQuery('#inCodModalidade').removeOption(/./); \n";
            $stJs .= "jQuery('#inCodModalidade').addOption('','Selecione');\n";
        }
    break;

    case 'incluirDocumento':
        $boErro = false;

        $arDocumento = Sessao::read('arDocumentos');

        if (count($arDocumento) > 0) {
            foreach ($arDocumento as $documento) {
                if( $_REQUEST['inCodDocumento'] == $documento['cod_documento'] )
                    $boErro = true;
            }
        }

        $nomeDocumento = Sessao::read("nomFiltro['documento'][".$_REQUEST['inCodDocumento']."]");

        if (!$boErro) {

           $inCount = sizeof($arDocumento);

           $arDocumento[$inCount]['cod_documento'] = $_REQUEST['inCodDocumento'];
           $arDocumento[$inCount]['nom_documento'] = $nomeDocumento;
           $arDocumento[$inCount]['modalidade'] = 'false';

           Sessao::write("arDocumentos", $arDocumento);

           $inQuantidadeDocExcluidos = Sessao::read("inQuantidadeDocumentosExcluidos");

           if (empty($inQuantidadeDocExcluidos)) {
            $inQuantidadeDocExcluidos = 0;
           } else
            $inQuantidadeDocExcluidos--;

           Sessao::write("inQuantidadeDocumentosExcluidos",$inQuantidadeDocExcluidos);

           $stJs .= montaListaDocumento( $arDocumento );

        } else {
            $stJs .= "alertaAviso( 'O documento escolhido já está na lista!(".$_REQUEST['inCodDocumento']." - ".Sessao::read("nomFiltro['documento'][".$_REQUEST['inCodDocumento']."]").")','form','erro','".Sessao::getId()."' );";
        }
    break;

    case "preencheAlteracao":
        $codModalidade = explode('-',$_REQUEST['stModalidade']);
        $_REQUEST['inCodModalidade'] = trim($codModalidade[0]);
        $stJs.= "f.inCodTipoCotacao.value = '".$_REQUEST['inCodTipoLicitacao']."';\n";
        $stJs.= "f.inCodTipoObjeto.value = '".$_REQUEST['inCodTipoObjeto']."';\n";
        $stJs.= "recuperaRegimeExecucaoObra('".$_REQUEST['inCodTipoObjeto']."', '".$_REQUEST['inCodRegime']."');\n";
        if($_REQUEST['boJulgamento']!=1){
            $stJs.= "f.stMapaCompras.value = '".$_REQUEST['stMapaCompra'] ."';\n";
            $stJs.= "f.stChaveProcesso.value = '".$_REQUEST['stProcesso'] ."';\n";
            $stJs.= "f.inCodCriterio.value = '".$_REQUEST['inCodCriterio']."';\n";
        }

        //// buscando a descrição
        include_once ( CAM_GP_COM_MAPEAMENTO. 'TComprasMapa.class.php' );
        $obTComprasMapa = new TComprasMapa;
        $arMapa = explode ( '/', $_REQUEST['stMapaCompra'] );
        $obTComprasMapa->setDado ( 'cod_mapa' , $arMapa[0] );
        $obTComprasMapa->setDado ( 'exercicio' , $arMapa[1] );
        $obTComprasMapa->recuperaMapaObjeto ( $rsMapa );

        $txtTipoCotacao = $rsMapa->getCampo ( 'cod_tipo_licitacao' ) . ' - ' .$rsMapa->getCampo ( 'tipo_licitacao' ) ;
        $stJs .= "d.getElementById('stTipoCotacao').innerHTML     = '$txtTipoCotacao';\n";

        $entidade = explode ('-',$_REQUEST['stEntidade']);

        include_once(TLIC."TLicitacaoComissaoLicitacao.class.php");
        $obTLicitacaoComissaoLicitacao = new TLicitacaoComissaoLicitacao();
        $obTLicitacaoComissaoLicitacao->setDado('cod_licitacao',$_REQUEST['inCodLicitacao']);
        $obTLicitacaoComissaoLicitacao->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
        $obTLicitacaoComissaoLicitacao->setDado('cod_entidade'  , trim($entidade[0])            );
        $obTLicitacaoComissaoLicitacao->setDado('exercicio'     , Sessao::getExercicio()        );
        $obTLicitacaoComissaoLicitacao->recuperaComissaoLicitacao($rsComissaoLicitacao);

        include_once(TCOM."TComprasMapaItem.class.php");

        $inCount = 0;
        $inCodComissao = '';

        while (!$rsComissaoLicitacao->eof()) {
            if ($rsComissaoLicitacao->getCampo('equipe') == 'apoio') {
                $stJs.= "f.inCodComissaoApoio.value = '".$rsComissaoLicitacao->getCampo('cod_comissao')."';\n";
                buscaParticipantes( $rsComissaoLicitacao->getCampo('cod_comissao') ,'Comissão de Apoio' ,true );
            } else {
                $inCodComissao = $rsComissaoLicitacao->getCampo('cod_comissao');
                buscaParticipantes( $rsComissaoLicitacao->getCampo('cod_comissao')  , 'Comissão Permanente',true) ;
            }
            $rsComissaoLicitacao->proximo();
        }
        
        
        include_once(TLIC."TLicitacaoLicitacao.class.php");
        $obLicitacaoLicitacao = new TLicitacaoLicitacao;
        $obLicitacaoLicitacao->setDado('cod_licitacao' , $_REQUEST['inCodLicitacao']   );
        $obLicitacaoLicitacao->setDado('cod_modalidade', $_REQUEST['inCodModalidade']  );
        $obLicitacaoLicitacao->setDado('cod_entidade'  , trim($entidade[0])            );
        $obLicitacaoLicitacao->setDado('exercicio'     , Sessao::getExercicio()        );
        $obLicitacaoLicitacao->recuperaPorChave($rsLicitacao);

        $dtLicitacao = SistemaLegado::dataToBr(substr($rsLicitacao->getCampo("timestamp"),0,10));
    
        $stJs .= "f.stDtLicitacao.value='".$dtLicitacao."';";

        if ($rsLicitacao->getCampo('registro_precos') == 't') {
            $txtRegistroPrecos = 'Sim';
            $inTipoRegistroPrecos = 1;
            $stCodModalidade = 'IN (3,6,7)';
        } else {
            $txtRegistroPrecos = 'Não';
            $inTipoRegistroPrecos = 0;
            $stCodModalidade = 'NOT IN (4,5,10,11)';
        }

        $stJs .= "d.getElementById('txtTipoRegistroPrecos').innerHTML = '$txtRegistroPrecos';\n";
        $stJs .= "d.getElementById('boHdnTipoRegistroPrecos').value = '$inTipoRegistroPrecos';\n";

        
        include_once(TLIC."TLicitacaoTipoChamadaPublica.class.php");
        $obLicitacaoTipoChamadaPublica = new TLicitacaoTipoChamadaPublica;
        $obLicitacaoTipoChamadaPublica->setDado('cod_tipo',$rsLicitacao->getCampo("tipo_chamada_publica"));
        $obLicitacaoTipoChamadaPublica->recuperaPorChave($rsTipoChamadaPublica);
            
        switch ($_REQUEST['inCodModalidade']) {
            case 8:
            case 9:
                $obRadioChamadaPublicaSim = new Radio;
                $obRadioChamadaPublicaSim->setRotulo     ('Chamada Pública');
                $obRadioChamadaPublicaSim->setLabel      ('Sim');
                $obRadioChamadaPublicaSim->setName       ('boRegistroModalidade');
                $obRadioChamadaPublicaSim->setId         ('boRegistroModalidade');
                $obRadioChamadaPublicaSim->setTitle      ('Informe se existe chamada pública.');
                $obRadioChamadaPublicaSim->setValue      (2);
                $obRadioChamadaPublicaSim->setNull       (false);
                $obRadioChamadaPublicaSim->setChecked    (false);
        
                $obRadioChamadaPublicaNao = new Radio;
                $obRadioChamadaPublicaNao->setLabel   ('Não');
                $obRadioChamadaPublicaNao->setTitle   ('Informe se existe chamada pública.');
                $obRadioChamadaPublicaNao->setName    ('boRegistroModalidade');
                $obRadioChamadaPublicaNao->setId      ('boRegistroModalidade');
                $obRadioChamadaPublicaNao->setValue   (0);
                $obRadioChamadaPublicaNao->setNull    (false);
                $obRadioChamadaPublicaNao->setChecked (true);

                if ($rsTipoChamadaPublica->getCampo('cod_tipo') != 0) {
                    $obRadioChamadaPublicaSim->setChecked (true);
                    $obRadioChamadaPublicaNao->setChecked (false);
                }

                $obFormulario = new Formulario();
                $obFormulario->agrupaComponentes(array($obRadioChamadaPublicaSim,$obRadioChamadaPublicaNao));
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();
                $stJs .= "d.getElementById('spnRegistroModalidade').innerHTML = '".$stHTML."';\n";
            break;
            
            case 10:
                $obRadioChamadaPublicaDispensa = new Radio;
                $obRadioChamadaPublicaDispensa->setRotulo     ('Tipo de Chamada Pública');
                $obRadioChamadaPublicaDispensa->setLabel      ('Dispensa por Chamada Pública');
                $obRadioChamadaPublicaDispensa->setName       ('boRegistroModalidade');
                $obRadioChamadaPublicaDispensa->setId         ('boRegistroModalidade');
                $obRadioChamadaPublicaDispensa->setTitle      ('Informe se é por dispensa.');
                $obRadioChamadaPublicaDispensa->setValue      (1);
                $obRadioChamadaPublicaDispensa->setNull       (false);
        
                $obRadioChamadaPublicaInexigibilidade = new Radio;
                $obRadioChamadaPublicaInexigibilidade->setLabel   ('Inexigibilidade por Chamada Pública');
                $obRadioChamadaPublicaInexigibilidade->setTitle   ('Informe se é por inexigibilidade.');
                $obRadioChamadaPublicaInexigibilidade->setName    ('boRegistroModalidade');
                $obRadioChamadaPublicaInexigibilidade->setId      ('boRegistroModalidade');
                $obRadioChamadaPublicaInexigibilidade->setValue   (2);
                $obRadioChamadaPublicaInexigibilidade->setNull    (false);

                if ($rsTipoChamadaPublica->getCampo('cod_tipo') != 0) {
                    if ($rsTipoChamadaPublica->getCampo('cod_tipo') == 1) {
                        $obRadioChamadaPublicaDispensa->setChecked(true);
                    } else {
                        $obRadioChamadaPublicaInexigibilidade->setChecked(true);
                    }
                }

                $obFormulario = new Formulario();
                $obFormulario->agrupaComponentes(array($obRadioChamadaPublicaDispensa,$obRadioChamadaPublicaInexigibilidade));
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();
                $stJs .= "d.getElementById('spnRegistroModalidade').innerHTML = '".$stHTML."';\n";
            break;
            
            default:
                $stJs .= "d.getElementById('spnRegistroModalidade').innerHTML = '';\n";
            break;
        }
        
        include_once(TLIC."TLicitacaoMembroAdicional.class.php");
        $obTLicitacaoMembroAdicional = new TLicitacaoMembroAdicional();
        $obTLicitacaoMembroAdicional->setDado('cod_licitacao',$_REQUEST['inCodLicitacao']);
        $obTLicitacaoMembroAdicional->setDado('cod_entidade',trim($entidade[0]));
        $obTLicitacaoMembroAdicional->setDado('exercicio',Sessao::getExercicio());
        $obTLicitacaoMembroAdicional->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
        $obTLicitacaoMembroAdicional->recuperaMembroAdicional($rsMembroAdicional);

        $inCount = 0;
        $arMembro = array();
        Sessao::remove('arMembro');

        while (!$rsMembroAdicional->eof()) {

            $arMembro[$inCount]['nom_cgm']            = $rsMembroAdicional->getCampo('nom_cgm');
            $arMembro[$inCount]['num_cgm']            = $rsMembroAdicional->getCampo('numcgm');
            $arMembro[$inCount]['cargo_membro']       = $rsMembroAdicional->getCampo('cargo');
            $arMembro[$inCount]['cod_natureza_cargo'] = $rsMembroAdicional->getCampo('natureza_cargo');
            $arMembro[$inCount]['adicional']          = 'Sim';
            $inCount++;
            $rsMembroAdicional->proximo();
        }
        Sessao::write("arMembro", $arMembro);

        $stJs .= montaListaMembroAdicional($arMembro);

        if ($_REQUEST['stMapaCompra']) {
            $obTComprasMapaItem = new TComprasMapaItem();
            $mapaCompras = explode("/",$_REQUEST['stMapaCompra']);
            $obTComprasMapaItem->setDado('cod_mapa',$mapaCompras[0]);
            $obTComprasMapaItem->setDado('exercicio',$mapaCompras[1]);
            $obTComprasMapaItem->recuperaValorTotal($rsVlTotal);
            if ($rsVlTotal) {
                $stJs .= "document.getElementById('stValorReferencial').value = '". number_format( $rsVlTotal->getCampo('vl_total'), 2 , ',' , '.' )  ."' ;\n";
                $stJs .= "d.getElementById('stValorReferencia').innerHTML = '". number_format( $rsVlTotal->getCampo('vl_total'), 2 , ',' , '.' )  ."' ;\n";
            } else {
                $stJs .= "d.getElementById('stValorReferencia').innerHTML = '&nbsp;'";
                $stJs .= "f.stValorReferencia.value = '';\n";
            }
        }

        $obTComprasObjeto = new TComprasObjeto();
        $obTComprasObjeto->setDado('cod_objeto',$_REQUEST['stCodObjeto']);
        $obTComprasObjeto->recuperaObjeto($rsObjeto);
        
        if(!$boEdital) {
            $stJs .= "f.stObjeto.value = '".$_REQUEST['stCodObjeto'] ."';\n";
            $stJs .= "parent.frames['telaPrincipal'].document.getElementById('txtObjeto').innerHTML = '".nl2br(addslashes(str_replace("\r\n", "\n", preg_replace("/(\r\n|\n|\r)/", "", $rsObjeto->getCampo('descricao')))))."';\n";
        }
        
        include_once(TLIC."TLicitacaoModalidadeDocumentos.class.php");
        $obTLicitacaoModalidadeDocumentos = new TLicitacaoModalidadeDocumentos();
        $obTLicitacaoModalidadeDocumentos->setDado('cod_modalidade',trim($codModalidade[0]));
        if ($_REQUEST['inCodModalidade'] != "") {
            $obTLicitacaoModalidadeDocumentos->recuperaDocumentos($rsDocumentos);
        }

        $arDocumentos = Sessao::read('arDocumentos');

        $boSessao = false;
        if (count($arDocumentos)>0) {
            Sessao::write('arDocumentosAux', $arDocumentos);
            Sessao::remove('arDocumentos');
            $boSessao = true;
        }

        unset($arDocumentos);

        $inCount = 0;
        if ($rsDocumentos) {
            while (!$rsDocumentos->eof()) {
                $arDocumentos[$inCount]['nom_documento'] = $rsDocumentos->getCampo('nom_documento');
                $arDocumentos[$inCount]['cod_documento'] = $rsDocumentos->getCampo('cod_documento');
                $arDocumentos[$inCount]['modalidade'] = 'true';
                $arDocumentosInseridosNaSessao[$inCount] == $rsDocumentos->getCampo('cod_documento');
                $inCount++;
                $rsDocumentos->proximo();
            }
        }

        $arDocumentosAux = Sessao::read('arDocumentosAux');

        if ($boSessao) {
            foreach ($arDocumentosAux as $key =>  $value) {
                if ( !in_array($value['cod_documento'],$arDocumentosInseridosNaSessao)) {
                    if ($value['modalidade'] == 'false') {
                        $arDocumentos[$inCount]['nom_documento'] = $value['nom_documento'];
                        $arDocumentos[$inCount]['cod_documento'] = $value['cod_documento'];
                        $arDocumentos[$inCount]['modalidade'] = $value['modalidade'];
                        $inCount++;
                    }
                }
                }
        }

        // Busca os documentos cadastrados para essa licitação
        include_once(TLIC."TLicitacaoLicitacaoDocumentos.class.php");
        $obTLicitacaoDocumentos = new TLicitacaoLicitacaoDocumentos();
        $obTLicitacaoDocumentos->setDado('cod_licitacao',$_REQUEST['inCodLicitacao']);
        $obTLicitacaoDocumentos->setDado('cod_entidade',trim($entidade[0]));
        $obTLicitacaoDocumentos->setDado('exercicio',Sessao::getExercicio());
        $obTLicitacaoDocumentos->setDado('cod_modalidade',$_REQUEST['inCodModalidade']);
        $obTLicitacaoDocumentos->recuperaDocumentosLicitacao($rsDocumentosLicitacao);

        while (!$rsDocumentosLicitacao->eof()) {
            $arDocumentos[$inCount]['nom_documento'] = $rsDocumentosLicitacao->getCampo('nom_documento');
            $arDocumentos[$inCount]['cod_documento'] = $rsDocumentosLicitacao->getCampo('cod_documento');
            $arDocumentos[$inCount]['modalidade'] = 'true';
            $inCount++;
            $rsDocumentosLicitacao->proximo();
        }

        Sessao::write("arDocumentos", $arDocumentos);

        if (Sessao::read('arDocumentos') != null)
           $stJs .= montaListaDocumento($arDocumentos);
           
           
        $obMaxMin = new Label();
        
        $obMaxMin->setName('stValor');

        $obMaxMin->setValue(  number_format( $_REQUEST['vlCotado']  , 2 , ',' , '.' )  );

        if (trim($codModalidade[0])  == 4) {
            $obMaxMin->setRotulo('Valor Mínimo');
        } else {
            $obMaxMin->setRotulo('Valor Máximo');
        }

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obMaxMin);
        $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML();
        $stJs .= "d.getElementById('spnMaxMin').innerHTML = '".$stHTML."';\n";
        $stJs .= "f.inCGM.focus();\n";
        $stJs .= " jq('#stChaveProcesso').focus(); \n ";
    break;

    case "limpar":
        Sessao::remove('transf3');
    break;

case 'limpaListas':
    Sessao::remove('arDocumentos');
    Sessao::remove('arDocumentosExcluidos');
    Sessao::remove('arMembro');
    Sessao::remove('arMembros');
    break;

case 'validaMapa':
    if ($_REQUEST['stMapaCompras'] != '' and $_REQUEST['stDtLicitacao'] != '') {
        $stDataVigencia = $_REQUEST['stDtLicitacao'];
        $arMapa = array();
        $arMapa = explode('/',$_REQUEST['stMapaCompras']);

        include ( CAM_GP_COM_MAPEAMENTO."TComprasMapaSolicitacao.class.php" );
        $obTComprasMapaSolicitacao = new TComprasMapaSolicitacao();
        $obTComprasMapaSolicitacao->setDado('cod_mapa' , $arMapa[0]);
        $obTComprasMapaSolicitacao->setDado('exercicio', Sessao::getExercicio() );
        $obTComprasMapaSolicitacao->recuperaMaiorDataSolicitacaoMapa($rsRecordSet);

        if ($rsRecordSet->getNumLinhas() > 0) {
            if (!SistemaLegado::comparaDatas($_REQUEST['stDtLicitacao'],$rsRecordSet->getCampo('dt_solicitacao'),true)) {
               $stJs .= "alertaAviso( 'A data da Licitação deve ser igual ou maior do que a maior data das solicitações do mapa (".$rsRecordSet->getCampo('dt_solicitacao').").','form','erro','".Sessao::getId()."');";
               $stJs .= "f.stDtLicitacao.value='';";
               $stJs .= "f.stDtLicitacao.focus();";
               $stDataVigencia = "";
            }
        }
    }else
        $stDataVigencia = "";

    $stJs .= montaComissao($stDataVigencia);
    break;

case 'validaDtLicitacao':
    if ($_REQUEST['stMapaCompras'] != '' and $_REQUEST['stDtLicitacao'] != '') {
        $stDataVigencia = $_REQUEST['stDtLicitacao'];

        $arMapa = array();
        $arMapa = explode('/',$_REQUEST['stMapaCompras']);

        include ( CAM_GP_COM_MAPEAMENTO."TComprasMapaSolicitacao.class.php" );
        $rsRecordSet = new RecordSet;
        $obTComprasMapaSolicitacao = new TComprasMapaSolicitacao;
        $obTComprasMapaSolicitacao->setDado( 'cod_mapa'  , $arMapa[0]);
        $obTComprasMapaSolicitacao->setDado( 'exercicio' , $arMapa[1]);
        $obTComprasMapaSolicitacao->recuperaMaiorDataSolicitacaoMapa($rsRecordSet);

        if ($rsRecordSet->getNumLinhas() > 0) {
            if (!SistemaLegado::comparaDatas($_REQUEST['stDtLicitacao'],$rsRecordSet->getCampo('dt_solicitacao'),true)) {
               $stJs .= "alertaAviso( 'A data da Licitação deve ser igual ou maior do que a maior data das solicitações do mapa (".$rsRecordSet->getCampo('dt_solicitacao').").','form','erro','".Sessao::getId()."');";
               $stJs .= "f.stDtLicitacao.value='';";
               $stJs .= "f.stDtLicitacao.focus();";
               $stDataVigencia = "";
            }
        }
    }else
        $stDataVigencia = "";

    $stJs .= montaComissao($stDataVigencia);

break;

    case 'recuperaUltimaDataContabil' :
        include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php";
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;

        $stFiltro = "   AND  empenho.cod_entidade = ".$_REQUEST['inCodEntidade']." \n";
        $stFiltro.= "   AND  empenho.exercicio = '".Sessao::getExercicio()."'      \n";
        $stOrdem  = " ORDER  BY empenho.dt_empenho DESC LIMIT 1                    \n";

        $obTEmpenhoEmpenho->recuperaUltimaDataEmpenho( $rsRecordSet,$stFiltro,$stOrdem );

        $dataUltimoEmpenho = SistemaLegado::dataToBr($rsRecordSet->getCampo('dt_empenho'));

        /*
            Rotina que serve para preencher a data da Licitação com
            a última data do lançamento contábil.
        */
        include_once( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );
        $obREmpenhoAutorizacaoEmpenho = new REmpenhoAutorizacaoEmpenho;

        $obREmpenhoAutorizacaoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade']);
        $obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );
        $obErro = $obREmpenhoAutorizacaoEmpenho->listarMaiorData( $rsMaiorData );

        if (($rsMaiorData->getCampo( "data_autorizacao" ) !="") ) {
            $stDtAutorizacao = $rsMaiorData->getCampo( "data_autorizacao" );
            $stExercicioDtAutorizacao = substr($stDtAutorizacao, 6, 4);
        } elseif ( ( $dataUltimoEmpenho !="") ) {
            $stDtAutorizacao = $dataUltimoEmpenho;
            $stExercicioDtAutorizacao = substr($dataUltimoEmpenho, 6, 4);
        } else {
            $stDtAutorizacao = "01/01/".Sessao::getExercicio();
            $stExercicioDtAutorizacao = Sessao::getExercicio();
        }

        // Preenche o campo Data da Licitação.
        $stJs .= "$('stDtLicitacao').value = '".$stDtAutorizacao."';\n";
        $stJs .= montaComissao($stDtAutorizacao);
    break;

    case 'recuperaRegimeExecucaoObra' :
        $inCodUF = SistemaLegado::pegaConfiguracao('cod_uf');
        switch ($request->get('inCodTipoObjeto')) {
            case 2:
                //TCE-MG ou TCM-GO
                if ( ($inCodUF == 11) || ($inCodUF == 9) ) {
                    include_once CAM_GP_LIC_MAPEAMENTO."TRegimeExecucaoObras.class.php";
                    $obTRegimeExecucaoObras = new TRegimeExecucaoObras;
                    $obTRegimeExecucaoObras->recuperaTodos($rsRecordSet);

                    $obSlRegime = new Select();
                    $obSlRegime->setRotulo    ( "Regime de execução de Obras"                            );
                    $obSlRegime->setName      ( "inCodRegime"                                            );
                    $obSlRegime->setTitle     ( "Regime de execução para obras e serviços de engenharia.");
                    $obSlRegime->setNull      ( false                                   );
                    $obSlRegime->setValue     ( $_REQUEST['inCodRegime']                );
                    $obSlRegime->addOption    ( "","Selecione"                          );
                    $obSlRegime->setCampoID   ( "cod_regime"                            );
                    $obSlRegime->setCampoDesc ( "descricao"                             );
                    $obSlRegime->preencheCombo( $rsRecordSet                            );

                    $obFormulario = new Formulario();
                    $obFormulario->addComponente($obSlRegime);
                    $obFormulario->montaInnerHTML();
                    $stHTML = $obFormulario->getHTML();
                    $stJs .= "d.getElementById('spnRegime').innerHTML = '".$stHTML."';\n";
                }
            break;
            
            default:
                $stJs .= "d.getElementById('spnRegime').innerHTML = '".$stHTML."';\n";
            break;
        }
       
    break;

    case 'recuperaRegistroModalidade':
        
        switch ($_REQUEST['inCodModalidade']) {
            case 8:            
            case 9:
                $obRadioChamadaPublicaSim = new Radio;
                $obRadioChamadaPublicaSim->setRotulo     ('Chamada Pública');
                $obRadioChamadaPublicaSim->setLabel      ('Sim');
                $obRadioChamadaPublicaSim->setName       ('boRegistroModalidade');
                $obRadioChamadaPublicaSim->setId         ('boRegistroModalidade');
                $obRadioChamadaPublicaSim->setTitle      ('Informe se existe chamada pública.');
                $obRadioChamadaPublicaSim->setValue      (2);
                $obRadioChamadaPublicaSim->setNull       (false);
                $obRadioChamadaPublicaSim->setChecked    (false);
        
                $obRadioChamadaPublicaNao = new Radio;
                $obRadioChamadaPublicaNao->setLabel   ('Não');
                $obRadioChamadaPublicaNao->setTitle   ('Informe se existe chamada pública.');
                $obRadioChamadaPublicaNao->setName    ('boRegistroModalidade');
                $obRadioChamadaPublicaNao->setId      ('boRegistroModalidade');
                $obRadioChamadaPublicaNao->setValue   (0);
                $obRadioChamadaPublicaNao->setNull    (false);
                $obRadioChamadaPublicaNao->setChecked (true);

                $obFormulario = new Formulario();
                $obFormulario->agrupaComponentes(array($obRadioChamadaPublicaSim,$obRadioChamadaPublicaNao));
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();
                $stJs .= "d.getElementById('spnRegistroModalidade').innerHTML = '".$stHTML."';\n";
            break;
            
            case 10:
                $obRadioChamadaPublicaDispensa = new Radio;
                $obRadioChamadaPublicaDispensa->setRotulo     ('Tipo de Chamada Pública');
                $obRadioChamadaPublicaDispensa->setLabel      ('Dispensa por Chamada Pública');
                $obRadioChamadaPublicaDispensa->setName       ('boRegistroModalidade');
                $obRadioChamadaPublicaDispensa->setId         ('boRegistroModalidade');
                $obRadioChamadaPublicaDispensa->setTitle      ('Informe se é por dispensa.');
                $obRadioChamadaPublicaDispensa->setValue      (1);
                $obRadioChamadaPublicaDispensa->setNull       (false);
        
                $obRadioChamadaPublicaInexigibilidade = new Radio;
                $obRadioChamadaPublicaInexigibilidade->setLabel   ('Inexigibilidade por Chamada Pública');
                $obRadioChamadaPublicaInexigibilidade->setTitle   ('Informe se é por inexigibilidade.');
                $obRadioChamadaPublicaInexigibilidade->setName    ('boRegistroModalidade');
                $obRadioChamadaPublicaInexigibilidade->setId      ('boRegistroModalidade');
                $obRadioChamadaPublicaInexigibilidade->setValue   (2);
                $obRadioChamadaPublicaInexigibilidade->setNull    (false);

                $obFormulario = new Formulario();
                $obFormulario->agrupaComponentes(array($obRadioChamadaPublicaDispensa,$obRadioChamadaPublicaInexigibilidade));
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();
                $stJs .= "d.getElementById('spnRegistroModalidade').innerHTML = '".$stHTML."';\n";
            break;
            default:
                $stJs .= "d.getElementById('spnRegistroModalidade').innerHTML = '';\n";
            break;
        }

    break;
 
    case 'alterarMembroAdicional':
        $arMembro = Sessao::read('arMembro');
        
        foreach ( $arMembro as $value ) {
            if ( $value['num_cgm'] == $_REQUEST['num_cgm'] ) {
                $stJs .=" jQuery('#inCGM').val('".$value['num_cgm']."'); ";
                $stJs .=" jQuery('#inCGM').attr('readonly',true); ";
                $stJs .=" jQuery('#stNomCGM').html('".$value['nom_cgm']."'); ";
                $stJs .=" jQuery('#stCargoMembro').val('".$value['cargo_membro']."'); ";
                $stJs .=" jQuery('#inCodNaturezaCargoMembro').val(".$value['cod_natureza_cargo']."); ";
                $stJs .=" jQuery('#btIncluirMembroAdicional').val('Alterar'); ";
                $stJs .=" jQuery('#btIncluirMembroAdicional').removeAttr('onclick'); ";                                
                $stJs .=" jQuery('#btIncluirMembroAdicional').attr('onclick','JavaScript:if ( ValidaMembroAdicional() ) { montaParametrosGET( \'adicionarMembroAdicionalAlterado\', \'\', true  ); limpaFormularioMembroAdicional(); }'); ";
            }
        }

    break;
    
    case 'adicionarMembroAdicionalAlterado':
        $arMembro = Sessao::read('arMembro');
        
        foreach ($arMembro as $key => $value) {            
            if ( $value['num_cgm'] == $_REQUEST['inCGM'] ) {                
                $arAuxMembro[$key]['nom_cgm']            = $value['nom_cgm'];
                $arAuxMembro[$key]['num_cgm']            = $_REQUEST['inCGM'];
                $arAuxMembro[$key]['cargo_membro']       = $_REQUEST['stCargoMembro'];
                $arAuxMembro[$key]['cod_natureza_cargo'] = $_REQUEST['inCodNaturezaCargoMembro'];
                $arAuxMembro[$key]['adicional']          = $value['adicional'];
                $stJs .=" jQuery('#inCGM').removeAttr('readonly'); ";
                $stJs .=" jQuery('#btIncluirMembroAdicional').val('Incluir'); ";
                $stJs .=" jQuery('#btIncluirMembroAdicional').removeAttr('onclick'); ";                                
                $stJs .=" jQuery('#btIncluirMembroAdicional').attr('onclick','JavaScript:if ( ValidaMembroAdicional() ) { montaParametrosGET( \'incluirMembroAdicional\', \'\', true  ); limpaFormularioMembroAdicional(); }'); ";
            }else{
                $arAuxMembro[$key]['nom_cgm']            = $arMembro[$key]['nom_cgm'];
                $arAuxMembro[$key]['num_cgm']            = $arMembro[$key]['num_cgm'];
                $arAuxMembro[$key]['cargo_membro']       = $arMembro[$key]['cargo_membro'];
                $arAuxMembro[$key]['cod_natureza_cargo'] = $arMembro[$key]['cod_natureza_cargo'];
                $arAuxMembro[$key]['adicional']          = $arMembro[$key]['adicional'];
            }
        }
        
        $arMembro = $arAuxMembro;
        Sessao::write("arMembro",$arMembro);
        $stJs .= montaListaMembroAdicional($arMembro);

    break;

    case 'montaItensAlterar':
        list($inCodMapa, $stExercicioMapa) = explode('/', $_REQUEST['stMapaCompras']);
        $stExercicioMapa = ($stExercicioMapa == '') ? Sessao::getExercicio() : $stExercicioMapa;
        $boExecuta = false;
        if (($_REQUEST['hdnMapaCompras'] == ($inCodMapa.'/'.$stExercicioMapa)) || $_REQUEST['boAlteraAnula']) {
            $boExecuta = true;
        } else {
            if ($_REQUEST['stMapaCompras'] != '') {
                $boLicitacao = SistemaLegado::pegaDado ( "cod_licitacao","licitacao.licitacao"," where cod_mapa = " . $inCodMapa  . " and exercicio_mapa = '" . $stExercicioMapa . "'");
                $boCompraDireta = SistemaLegado::pegaDado ("cod_compra_direta","compras.compra_direta"," where cod_mapa = ".$inCodMapa." and exercicio_mapa ='".$stExercicioMapa."'" );

                include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapa.class.php';
                $obTComprasMapa = new TComprasMapa();
                $stFiltro .= " AND mapa.cod_mapa  = ".$inCodMapa;
                $stFiltro .= " AND mapa.exercicio = '".$stExercicioMapa."' ";
                $obTComprasMapa->recuperaMapaSemReservaProcessoLicitatorio($rsComprasMapa, $stFiltro);
                //Mantendo a validacao original
                if ( $rsComprasMapa->getNumLinhas() >= 1 ) {
                    $boExecuta = true;
                }
            }
        }

        if ($boExecuta) {
            include_once CAM_GP_COM_MAPEAMENTO.'TComprasMapaItem.class.php';
            include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoCatalogoItem.class.php';

            $obTAlmoxarifadoCatalogoItem = new TAlmoxarifadoCatalogoItem();
            $obTComprasMapaItem = new TComprasMapaItem();
            $obTComprasMapaItem->setDado('exercicio', $stExercicioMapa );
            $obTComprasMapaItem->setDado('cod_mapa' , $inCodMapa );
            $obTComprasMapaItem->recuperaItensCompraDireta( $rsMapaItens );
            // somar total do mapa
            while ( !$rsMapaItens->eof() ) {
                // Recupera o valor da última compra do ítem.
                $obTAlmoxarifadoCatalogoItem->setDado('cod_item'  , $rsMapaItens->getCampo('cod_item'));
                $obTAlmoxarifadoCatalogoItem->setDado('exercicio' , $stExercicioMapa);
                $obTAlmoxarifadoCatalogoItem->recuperaValorItemUltimaCompra($rsItemUltimaCompra);
                $rsMapaItens->setCampo('valor_ultima_compra', $rsItemUltimaCompra->getCampo('vl_unitario_ultima_compra') );
                $rsMapaItens->proximo();
            }
      
            $rsMapaItens->setPrimeiroElemento();
            
            $stJs = montaListaItens( $rsMapaItens ) ;
        } else {
            $stJs = "$('spnItens').innerHTML= '';\n";
        }
    break;
}

echo $stJs;

?>
