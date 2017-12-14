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
    * Página de Formulário para manter participantes
    * Data de Criação   : 06/10/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Maicon Brauwers

    * @ignore

    * $Id: OCManterManutencaoParticipante.php 62270 2015-04-15 20:13:46Z arthur $

    * Casos de uso : uc-03.05.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(TLIC."TLicitacaoLicitacao.class.php");
include_once(TLIC."TLicitacaoEdital.class.php");
include_once(TLIC."TLicitacaoParticipante.class.php");
include_once(TLIC."TLicitacaoParticipanteConsorcio.class.php");
include_once(CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterManutencaoParticipante";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

function montaSpanParticipante()
{
    include_once(CAM_GA_CGM_COMPONENTES."IPopUpCGMVinculado.class.php");

    $obForm = new Form();
    //tipo de participacao
    //isolado
    $obTipoPartIsolado = new Radio;
    $obTipoPartIsolado->setName('tipoParticipacao');
    $obTipoPartIsolado->setId('tipoPartIsolado');
    $obTipoPartIsolado->setValue('isolado');
    $obTipoPartIsolado->setLabel('Participante Isolado');
    $obTipoPartIsolado->setChecked(true);
    $obTipoPartIsolado->setRotulo('Tipo de Participação');
    $obTipoPartIsolado->setTitle('Infome o tipo de participante');

    //adiciona o evento/codigo javascript para fechar a span do cgm consorcio
    $jsFechaSpnCGMCons = "document.getElementById('trCGMConsorcio').style.display = 'none';";
    $obTipoPartIsolado->obEvento->setOnClick($jsFechaSpnCGMCons);

    //consorcio
    $obTipoPartConsorcio = new Radio;
    $obTipoPartConsorcio->setName('tipoParticipacao');
    $obTipoPartConsorcio->setId('tipoPartConsorcio');
    $obTipoPartConsorcio->setValue('consorcio');
    $obTipoPartConsorcio->setLabel('Consórcio');

    //adiciona o evento/codigo javascript para abrir a span do cgm consorcio
    $jsAbreSpnCGMCons = "document.getElementById('trCGMConsorcio').style.display = '';";
    $obTipoPartConsorcio->obEvento->setOnClick($jsAbreSpnCGMCons);

    $obCGMConsorcio = new IPopUpCGM($obForm);

    $obImagemCons = new Img;
    $obImagemCons->setCaminho   ( CAM_FW_IMAGENS."botao_popup.png");
    $obImagemCons->setAlign     ( "absmiddle" );
    $obImagemCons->setId        ( "imagemCons" );
    $obCGMConsorcio->setImagem  ( $obImagemCons );

    //isso configura o nome da variavel q possuira o valor descritivo do CGM
    $obCGMConsorcio->setName('stNomConsorcio');
    $obCGMConsorcio->setId('stNomConsorcio');

    //isso configura o nome da variavel q possuira o valor numerico da chave do CGM
    $obCGMConsorcio->obCampoCod->setName ( 'cgmConsorcio' );
    $obCGMConsorcio->obCampoCod->setId ( 'cgmConsorcio' );

    $obCGMConsorcio->setRotulo('*CGM do Consórcio');
    $obCGMConsorcio->setTitle('Informe o CGM do consórcio');
    $obCGMConsorcio->setNull(true);

    //cgm do participante
    $obCGMPart = new IPopUpCGMVinculado($obForm);

    $obImagemPart    = new Img;
    $obImagemPart->setCaminho   ( CAM_FW_IMAGENS."botao_popup.png");
    $obImagemPart->setAlign     ( "absmiddle" );
    $obImagemPart->setId        ( "imagemPart" );
    $obCGMPart->setImagem       ( $obImagemPart );

    //para configurar a tabela de vinculo
    $obCGMPart->setTabelaVinculo(' ( select * from compras.fornecedor where ativo = true )');
    $obCGMPart->setCampoVinculo('cgm_fornecedor');
    $obCGMPart->setNomeVinculo('Participante');

    //isso configura o nome da variavel q possuira o valor descritivo do CGM
    $obCGMPart->setName('stNomParticipante');
    $obCGMPart->setId('stNomParticipante');

    //isso configura o nome da variavel q possuira o valor numerico da chave do CGM
    $obCGMPart->obCampoCod->setName ( 'cgmParticipante' );
    $obCGMPart->obCampoCod->setId ( 'cgmParticipante' );

    $obCGMPart->setRotulo('CGM do Participante');
    $obCGMPart->setNull(true);
    $obCGMPart->setObrigatorioBarra(true);
    $obCGMPart->setTitle('Informe o CGM do participante da licitação');

    //cgm do representante legal
    $obCGMRep = new IPopUpCGM($obForm);

    //isso configura o nome da variavel q possuira o valor descritivo do CGM
    $obCGMRep->setName('stNomRepLegal');
    $obCGMRep->setId('stNomRepLegal');

    //isso configura o nome da variavel q possuira o valor numerico da chave do CGM
    $obCGMRep->obCampoCod->setName ( 'cgmRepLegal' );
    $obCGMRep->obCampoCod->setId ( 'cgmRepLegal' );
    $obCGMRep->setRotulo('CGM do Representante Legal');
    $obCGMRep->setNull(true);
    $obCGMRep->setObrigatorioBarra(true);
    $obCGMRep->setTitle('Informe o CGM do representante legal');

    //data de inclusao da licitacao
    //fazer esquema de verificao da data
    $obDataInclusao = new Data();
    $obDataInclusao->setName('dataInclusao');
    $obDataInclusao->setId('dataInclusao');
    $obDataInclusao->setRotulo('Data de inclusão na Licitação');
    $obDataInclusao->setNull(true);
    $obDataInclusao->setObrigatorioBarra(true);
    $obDataInclusao->setValue(date('d/m/Y', time()));
    $obDataInclusao->setTitle('Informe a data de inclusão do participante no processo');

    $obFormulario = new Formulario;
    $obFormulario->addForm ( $obForm );

    $obFormulario->agrupaComponentes    (array ($obTipoPartIsolado,$obTipoPartConsorcio));

    //cria a linha manualmente no formulario, para fazer o esquema de abrir/fechar o componente cgm consorcio
    $obLinhaConsorcio = new Linha();
    $obLinhaConsorcio->setId('trCGMConsorcio');

    //coloca inicialmente a linha invisivel
    $obLinhaConsorcio->setStyle('display: none');
    $obFormulario->setUltimaLinha($obLinhaConsorcio);
    $obFormulario->addComponente    ($obCGMConsorcio, false, true);

    $obFormulario->addComponente    ($obCGMPart);
    $obFormulario->addComponente    ($obCGMRep);
    $obFormulario->addComponente    ($obDataInclusao);

    //sub-formulario para inclusao de participante
    $obFormulario->Incluir ( 'ParticipanteLicitacao', array( $obTipoPartIsolado,$obTipoPartConsorcio,$obCGMConsorcio, $obCGMRep, $obCGMPart, $obDataInclusao) , false);

    $obFormulario->montaInnerHTML();

    $stJs.= "document.getElementById('spnParticipante').innerHTML = '".$obFormulario->getHTML()."';";

    return $stJs;
}

/**
 * Verifica se determinado item existe em um array de itens
 * Comparando os campos no array com os campos vindos por $_REQUEST
 */
function verificaItemExisteNaLista($itens,$campos)
{
    for ($i=0;$i<count($itens);$i++) {
        $item = $itens[$i];
        $todosDadosIguais = 1;
        foreach ($campos as $campo) {
            if ($item[$campo] != $_REQUEST[$campo]) {
                $todosDadosIguais = 0;
                break;
            }
        }

        if ($todosDadosIguais) {
            return true;
        }
    }

    return false;
}

/**
 * Verfica se determinado representante legal ja esta na lista de participantes
 */
function repLegalJaVinculado($cgmRepLegal,$arParticipantes)
{
    foreach ($arParticipantes as $part) {
        if ($part['cgmRepLegal'] == $cgmRepLegal) {
            return true;
        }
    }

    return false;
}

/**
 * Adiciona um novo item em uma lista
 */
function adicionaNovoItemLista(&$lista, $campos, $tiposCampos=array())
{
    $indice = count($lista);

    //percorre todos os campos e copia o dado do aray request para a lista
    foreach ($campos as $key=>$campo) {
        switch ($tiposCampos[$key]) {
            case 'int':
                $lista[$indice][$campo] = (int) $_REQUEST[$campo];
            break;

            case 'string':
            default:
                $lista[$indice][$campo] = $_REQUEST[$campo];
            break;
        }
    }

    //seta a variavel numOrdem, q mais tarde vai ser necessario para a alteracao/exclusao
    $lista[$indice]['numOrdem'] = $indice;

    return $indice;
}

/**
 * Imprime a lista dos participantes da licitacao
 */
function imprimeListaParticipantesLicitacao($arLista, $boExecuta = true)
{
    function addColCabecalho(&$obILista, $titulo, $width)
    {
        $obILista->addCabecalho();
        $obILista->ultimoCabecalho->addConteudo($titulo);
        $obILista->ultimoCabecalho->setWidth($width);
        $obILista->commitCabecalho();
    }

    /**
     * Adiciona a definicao do campo a ser listado
     */
    function addCampoDado(&$obILista, $nomeCampo, $alinhamento="ESQUERDA")
    {
        $obILista->ultimoDado->setCampo($nomeCampo);
        $obILista->ultimoDado->setAlinhamento($alinhamento);
        $obILista->commitDado();
    }

    /**
     * Adiciona uma acao
     */
    function addAcao(&$obILista, $acaoName, $scriptAcao, $camposParametros=array(), $funcaoAjax=true)
    {
        $obILista->addAcao();
        $obILista->ultimaAcao->setAcao($acaoName);
        $obILista->ultimaAcao->setFuncaoAjax($funcaoAjax);
        $obILista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('".$scriptAcao."');");
        
        foreach ($camposParametros as $k=>$campo) {
            $obILista->ultimaAcao->addCampo($k+1, $campo);
        }
        $obILista->commitAcao();
    }

    $rs = new RecordSet();
    $rs->preenche($arLista);

    //cria o objeto de interface de listagem
    $obListaPart = new Lista;
    $obListaPart->setMostraPaginacao( true );
    $obListaPart->setRecordSet( $rs );

    //imprime o cabecalho
    $obListaPart->addCabecalho();
    $obListaPart->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaPart->ultimoCabecalho->setWidth( 5 );
    $obListaPart->commitCabecalho();

    $obListaPart->addCabecalho();
    $obListaPart->ultimoCabecalho->addConteudo( "Participante" );
    $obListaPart->ultimoCabecalho->setWidth( 25 );
    $obListaPart->commitCabecalho();

    $obListaPart->addCabecalho();
    $obListaPart->ultimoCabecalho->addConteudo( "Representante Legal" );
    $obListaPart->ultimoCabecalho->setWidth( 25 );
    $obListaPart->commitCabecalho();

    $obListaPart->addCabecalho();
    $obListaPart->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaPart->ultimoCabecalho->setWidth( 5 );
    $obListaPart->commitCabecalho();

    $obListaPart->addDado();
    $obListaPart->ultimoDado->setAlinhamento("CENTRO");
    $obListaPart->ultimoDado->setCampo( "stNomParticipante" );
    $obListaPart->commitDado();

    $obListaPart->addDado();
    $obListaPart->ultimoDado->setAlinhamento("CENTRO");
    $obListaPart->ultimoDado->setCampo( "stNomRepLegal" );
    $obListaPart->commitDado();

    addAcao($obListaPart,"ALTERAR","alterarParticipante", array("numOrdem"));
    addAcao($obListaPart,"EXCLUIR","excluirParticipante", array("numOrdem"));

    $obListaPart->montaHTML();

    $stHTML = $obListaPart->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        $stJs .= "parent.frames['telaPrincipal'].document.getElementById('spnListaParticipantesLic').innerHTML = '".$stHTML."';";
        
        return $stJs;
    } else {
        return $stHTML;
    }
}

/**
 * Retorna o timestamp php a partir de um timestamp de data do postgres
 */
function getTimestampDataPGSql($data)
{
    sscanf($data, "%d-%d-%d %d:%d:%d.%d",
            $ano, $mes, $dia, $hora, $minuto, $segundo, $micros);
    
    return mktime($hora,$minuto,$segundo,$mes,$dia,$ano);
}

/*
 * Ajusta o formulario para alteracao de participante
 * Basicamente, carrega os valores passados por parametro no formulario
 */
function montaAlteracao($numOrdem)
{
    //apenas o cgm do representante legal pode ser alterado
    Sessao::write('alterandoTransf7' , 1);
    Sessao::write('numOrdem', $numOrdem);
    $stJs='';

    //le o participante da sessao, atraves do numOrdem
    $arPart = Sessao::read('part');
    $part = $arPart[$numOrdem];

    if ($part['cgmConsorcio'] > 0) {
        $stJs.= "el = document.getElementById('tipoPartConsorcio');";
        $stJs.= "el.checked = true; el.disabled = true;";
        $stJs.= "el = document.getElementById('cgmConsorcio');";
        $stJs.= "el.value = '".$part['cgmConsorcio']."'; ";
        $stJs.= "el = document.getElementById('tipoPartIsolado');";
        $stJs.= "el.disabled = true;";
        //abre o elemento do consorcio
        $stJs.= "el = document.getElementById('trCGMConsorcio');";
        $stJs.= "el.style.display = '';";
        $stJs.= "document.getElementById('cgmConsorcio').disabled = true;";
        $stJs .= " document.getElementById('imagemCons').style.display  = 'none';";
        $stJs.= "el = document.getElementById('stNomConsorcio');";
        $stJs.= "el.innerHTML = '".$part['stNomConsorcio']."'; el.disabled = true;";
    } else {
        $stJs.= "el = document.getElementById('tipoPartIsolado');";
        $stJs.= "el.checked = true; el.disabled = true;";
        $stJs.= "el = document.getElementById('tipoPartConsorcio');";
        $stJs.= "el.disabled = true;";
        $stJs.= "el = document.getElementById('trCGMConsorcio');";
        $stJs.= "el.style.display = 'none';";
        $stJs.= "document.getElementById('cgmConsorcio').disabled = true;";
    }

    $stJs.= "el = document.getElementById('cgmParticipante');";
    $stJs.= "el.value = '".$part['cgmParticipante']."'; el.disabled = true;";

    $stJs.= "el = document.getElementById('stNomParticipante');";
    $stJs.= "el.innerHTML = '".$part['stNomParticipante']."'; el.disabled = true;";

    $stJs .= " document.getElementById('imagemPart').style.display  = 'none';";

    $stJs.= "el = document.getElementById('dataInclusao');";
    $stJs.= "el.value = '".$part['dataInclusao']."'; el.disabled = true;";

    $stJs.= "el = document.getElementById('cgmRepLegal');";
    $stJs.= "el.value = '".$part['cgmRepLegal']."'; el.focus();";

    $stJs.= "el = document.getElementById('stNomRepLegal');";
    $stJs.= "el.innerHTML = '".$part['stNomRepLegal']."'; el.disabled = true;";

    $stJs.= "document.frm.btLimparParticipanteLicitacao.disabled = true; ";
    $stJs.= "document.frm.btIncluirParticipanteLicitacao.value='Alterar'; ";

    for ($i=1; $i<=count($arPart); $i++) {
        $stJs.= "jq('[id^=_".$i."]').css({visibility: 'hidden'});";
    }

    return $stJs;
}

/*
 * Executa alteracao de participante no array de sessao
 */
function executaAlteracao($cgmRepLegal,$nomeRepLegal)
{
    $numOrdem = Sessao::read('numOrdem');
    $arPart = Sessao::read('part');
    //altera o cgm do representante legal na sessao, se este foi modificado
    if ($arPart[$numOrdem]['cgmRepLegal'] != $cgmRepLegal) {
        $arPart[$numOrdem]['cgmRepLegal'] = $cgmRepLegal;
        $arPart[$numOrdem]['stNomRepLegal'] = $nomeRepLegal;
    }
    Sessao::write('part', $arPart);
    Sessao::remove('alterandoTransf7');

    //O formulario volta a ser de inclusao
    $stJs='';
    $stJs.="document.getElementById('tipoPartIsolado').disabled = false; ";
    $stJs.="document.getElementById('tipoPartIsolado').checked = true; ";
    $stJs.="document.getElementById('tipoPartConsorcio').disabled = false; ";
    $stJs.="document.getElementById('tipoPartConsorcio').checked = false; ";
    $stJs.="document.getElementById('cgmConsorcio').disabled = false; ";
    $stJs.="document.getElementById('cgmParticipante').disabled = false; ";
    $stJs.= "document.getElementById('imagemPart').style.display  = '';";
    $stJs.= "document.getElementById('imagemCons').style.display  = '';";
    $stJs.="document.getElementById('dataInclusao').disabled = false; ";
    $stJs.="document.getElementById('trCGMConsorcio').style.display = 'none';";
    $stJs.="document.getElementById('cgmConsorcio').value = '';";
    $stJs.="document.getElementById('stNomConsorcio').innerHTML = '&nbsp;';";
    $stJs.= "document.frm.btLimparParticipanteLicitacao.disable = false; ";

    $stJs.= "document.frm.btIncluirParticipanteLicitacao.value='Incluir'; ";
    $stJs.= "limpaFormularioParticipanteLicitacao();";
    $stJs.= imprimeListaParticipantesLicitacao($arPart);

    for ($i=1; $i<=count($arPart); $i++) {
        $stJs.= "jq('[id^=_".$i."]').css({visibility: 'visible'});";
    }

    return $stJs;
}

function verificaParticipanteDebitoTributario($cgmParticipante)
{
    include_once ( CAM_GP_COM_MAPEAMENTO."TComprasFornecedor.class.php");

    $boTemDebitoTributario = false;
    $obTComprasFornecedor = new TComprasFornecedor;

    $stFiltroSQL .= "WHERE calculo_cgm.numcgm = ".$cgmParticipante." \n";

    $obTComprasFornecedor->recuperaFornecedorDebito ( $rsFornecedor, $stFiltroSQL );

    if ($rsFornecedor->getNumLinhas() > 0) {
        $boTemDebitoTributario = true;
    }

    return $boTemDebitoTributario;
}

function insereParticipante()
{
    $arPart = Sessao::read('part');

    //os campos e os tipos dos campos
    $campos = array('tipoParticipacao','cgmConsorcio','stNomConsorcio','cgmParticipante','stNomParticipante','cgmRepLegal','stNomRepLegal','dataInclusao');
    $tiposCampos = array('string','int','string','int','string','int','string','string');

    //limpa o formulario manualmente
    $stJs.= "limpaFormularioParticipanteLicitacao();";

    //adiciona o novo item na lista
    $indice = adicionaNovoItemLista($arPart, $campos, $tiposCampos);

    Sessao::write('part',$arPart);

    $stJs.= imprimeListaParticipantesLicitacao($arPart);

    return $stJs;
}

switch ($stCtrl) {
    case 'incluirParticipanteLicitacao':
    
        if (Sessao::read('alterandoTransf7')) {
            //esta alterando, executa a alteracao
            $stJs.= executaAlteracao($_REQUEST['cgmRepLegal'],$_REQUEST['stNomRepLegal']);
            break;
        }
        
        list($ano, $mes, $dia) = array_reverse(explode('/',$_REQUEST['dataInclusao']));
        $timestampInclusao = $ano.$mes.$dia;

        list($ano, $mes, $dia) = explode('-',substr(Sessao::read('timestampCriacaoLicitacao'), 0, 11));
        $timestampCriacao = $ano.$mes.$dia;

        if (intval($timestampInclusao) < intval($timestampCriacao)) {
            //erro: a data de inclusao eh menor q a data de criacao da licitacao
            $stJs.= "alertaAvisoTelaPrincipal('A data de inclusão do participante é menor que a data de criação da licitação.','form','erro','".Sessao::getId()."');";
            $stJs.= "document.getElementById('dataInclusao').focus();";
            break;
        }

        //confere a data de adjudicacao
        $dtTimestampAdjudicacaoLicitacao = Sessao::read('timestampAdjudicacaoLicitacao');
        if ($dtTimestampAdjudicacaoLicitacao) {
            $timestampAdjudicacao = getTimestampDataPGSql($dtTimestampAdjudicacaoLicitacao);
            if ($timestampInclusao > $timestampAdjudicacao) {
                //erro: a data de inclusao eh menor q a data de criacao da licitacao
                $stJs.="alertaAvisoTelaPrincipal('A data de inclusão do participante é maior do que a data de adjudicação da licitação.','form','erro','".Sessao::getId()."');";
                $stJs.= "document.getElementById('dataInclusao').focus();";
                break;
            }
        }

        //verifica se o item ja existe na lista
        $jaExisteItemLista=0;

        $arPart = Sessao::read('part');
        if ($arPart) {
            //verificar o elemento já existe
            $jaExisteItemLista = verificaItemExisteNaLista($arPart, array('cgmParticipante'));
        }

        if ($jaExisteItemLista) {
            $stJs.= "d.getElementById('cgmParticipante').focus();";
            $stJs.="alertaAvisoTelaPrincipal('Esse participante já está na lista de participantes','form','erro','".Sessao::getId()."');";
            break;
        } else {
            if (repLegalJaVinculado((int) $_REQUEST['cgmRepLegal'], $arPart)) {
                $stJs.= "d.getElementById('cgmRepLegal').focus();";
                $stJs.="alertaAvisoTelaPrincipal('Esse representante legal já está vinculado a outro participante.','form','erro','".Sessao::getId()."');";
            } else {

                $boPodeIncluir = false;

                $boTemDebitoTributario = verificaParticipanteDebitoTributario($_REQUEST['cgmParticipante']);

                if ($boTemDebitoTributario == true) {
                    $stJs.="confirmPopUp('Manutenção de Participantes','Esse participante possui débitos tributários, deseja realmente inseri-lo na lista de participantes?','montaParametrosGET(\'insereParticipante\')');";
                } else {
                   $stJs.= insereParticipante();
                }
            }
            $stJs.="document.getElementById('trCGMConsorcio').style.display = 'none';";
            $stJs.="document.getElementById('tipoPartIsolado').checked = true; ";
        }
    break;

    case 'insereParticipante':
        $stJs.= insereParticipante();
    break;

    case 'alterarParticipante':
        //alteracao de participante de licitacao
        $stJs.= montaAlteracao((int) $_REQUEST['numOrdem']);
    break;

    case 'exibeLicitacao':
                
        $obTLicitacaoLicitacao = new TLicitacaoLicitacao;
        $obTLicitacaoLicitacao->setDado('exercicio'      , $request->get('stExercicioLicitacao') );
        $obTLicitacaoLicitacao->setDado('cod_licitacao'  , $request->get('inCodLicitacao')       );
        $obTLicitacaoLicitacao->setDado('cod_modalidade' , $request->get('inCodModalidade')      );
        $obTLicitacaoLicitacao->setDado('cod_entidade'   , $request->get('inCodEntidade')        );
        
        $arEdital = explode('/', $request->get('numEdital') );
        $numEdital       = $arEdital[0];
        $exercicioEdital = $arEdital[1];
        $obTLicitacaoLicitacao->setDado('num_edital', $arEdital[0] );
        
        
        //muda filtro de acordo com o tipo
        $stFiltro = "
            -- A Licitação não pode estar anulada.
            AND NOT EXISTS (
                                SELECT	1
                                  FROM	licitacao.licitacao_anulada
                                 WHERE	licitacao_anulada.cod_licitacao  = licitacao.cod_licitacao
                                   AND  licitacao_anulada.cod_modalidade = licitacao.cod_modalidade
                                   AND  licitacao_anulada.cod_entidade   = licitacao.cod_entidade
                                   AND  licitacao_anulada.exercicio      = licitacao.exercicio
                            )
                            
            -- O Edital não pode estar anulado.
            AND NOT EXISTS (
                                SELECT	1
                                  FROM	licitacao.edital_anulado
                                 WHERE  edital_anulado.num_edital = edital.num_edital
                                   AND 	edital_anulado.exercicio  = edital.exercicio
                            )
            
            -- Para as modalidades 1,2,3,4,5,6,7,10,11 é obrigatório exister um edital
            AND CASE WHEN licitacao.cod_modalidade in (1,2,3,4,5,6,7,10,11) THEN
                    
                    edital.cod_licitacao IS NOT NULL
               AND edital.cod_modalidade IS NOT NULL
               AND edital.cod_entidade   IS NOT NULL 
               AND edital.exercicio      IS NOT NULL 

              -- Para as modalidades 8,9 é facultativo possuir um edital
              WHEN licitacao.cod_modalidade in (8,9) THEN
                    
                    edital.cod_licitacao  IS NULL
                 OR edital.cod_modalidade IS NULL
                 OR edital.cod_entidade   IS NULL 
                 OR edital.exercicio      IS NULL 

	         OR edital.cod_licitacao  IS NOT NULL
	         OR edital.cod_modalidade IS NOT NULL
	         OR edital.cod_entidade   IS NOT NULL 
	         OR edital.exercicio      IS NOT NULL 
            END  \n ";
        
        $obTLicitacaoLicitacao->recuperaManutencaoParticipanteLicitacao( $rsLic, $stFiltro );

        # Verifica se a licitação pertencente ao edital não foi julgada.
        $obTLicitacaoLicitacao->recuperaLicitacaoNaoJulgada($rsLicitacaoNaoJulgada);

        # Se retornar registro, é por que possui julgamento.
        if ($rsLicitacaoNaoJulgada->getNumLinhas() > 0) {
            if ($rsLicitacaoNaoJulgada->getCampo('cod_cotacao') == '') { #Se não estiver anulado, não deve deixar reutilizá-la
                $stJs .= "alertaAviso('A licitação nº ".$rsLic->getCampo('cod_licitacao')." vinculada a esse Edital já foi julgada!', 'form','erro','".Sessao::getId()."'); window.location = '".$pgList."';" ;
                $obErro = true;
            }
        }

        if ($obErro) {
            # Controles para limpar o form.
            $stJs .= "document.getElementById('objetoLicitatorio').innerHTML = ''; ";
            $stJs .= "document.getElementById('spnEdital').innerHTML = ''; ";
            $stJs .= "document.getElementById('spnListaParticipantesLic').innerHTML = ''; ";
            $stJs .= "document.getElementById('spnParticipante').innerHTML = ''; ";
            break;
        }

        Sessao::write('cod_licitacao',  $rsLic->getCampo('cod_licitacao'));
        Sessao::write('cod_entidade',   $rsLic->getCampo('cod_entidade'));
        Sessao::write('cod_modalidade', $rsLic->getCampo('cod_modalidade'));
        Sessao::write('exercicio',      $rsLic->getCampo('exercicio'));

        //licitacao encontrada, mostra o objeto da licitacao
        $obComprasObjeto = new TComprasObjeto();
        $obComprasObjeto->setDado('cod_objeto', $rsLic->getCampo('cod_objeto'));
        $obComprasObjeto->recuperaPorChave($rsObjLic);
        $objetoLicitatorio = $rsLic->getCampo('cod_licitacao')."-".$rsLic->getCampo('cod_modalidade')."-".$rsLic->getCampo('cod_entidade')."-".$rsLic->getCampo('exercicio')."-".addslashes(nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsObjLic->getCampo("descricao")))));

        //seta o valor no span
        include_once ( CAM_GP_LIC_COMPONENTES. "ILabelNumeroLicitacao.class.php" );
        $obForm = new Form;
        $stJs.= "document.getElementById('objetoLicitatorio').innerHTML = '".$objetoLicitatorio."';";
        
        $obLblNumeroLicitacao = new ILabelNumeroLicitacao( $obForm );
        $obLblNumeroLicitacao->setExercicio   ( $rsLic->getCampo('exercicio') );
        $obLblNumeroLicitacao->setCodEntidade ( Sessao::read('cod_entidade')  );
        $obLblNumeroLicitacao->setNumLicitacao( Sessao::read('cod_licitacao') );
        $obLblNumeroLicitacao->setNumEdital   ( $numEdital );
        
        $obFormulario = new Formulario($obForm);
        //$obLblNumeroLicitacao->geraFormulario( $obFormulario );
        $obLblNumeroLicitacao->geraFormularioManutencaoParticipante( $obFormulario );
        $obFormulario->montaInnerHTML();

        $stJs.= "document.getElementById('spnEdital').innerHTML = '".$obFormulario->getHTML()."';";

        //coloca na sessao os valores da data de criacao e de adjudicacao da licitacao
        //para fazer a validacao da data de inclusao dos participantes
        Sessao::write('timestampCriacaoLicitacao', $rsLic->getCampo('timestamp'));
        Sessao::write('timestampLicitacao', substr($rsLic->getCampo('timestamp'),0,10));

        //agora recupera a data de adjudicacao
        //seta os outros dados da chave, usado para recuperar a adjudicacao
        $objLic = new TLicitacaoLicitacao;
        $objLic->setDado('cod_licitacao' , $rsLic->getCampo('cod_licitacao'));
        $objLic->setDado('cod_entidade'  , $rsLic->getCampo('cod_entidade'));
        $objLic->setDado('cod_modalidade', $rsLic->getCampo('cod_modalidade'));
        $objLic->setDado('exercicio'     , $rsLic->getCampo('exercicio'));

        $objLic->recuperaAdjudicacao($rsAdjudicacao);

        //coloca na sessao tb a data de adjudicacao
        Sessao::write('timestampAdjudicacaoLicitacao', $rsAdjudicacao->getCampo('timestamp'));

        //agora inicializa a lista
        $obMapParticipantes = new TLicitacaoParticipante;
        $obMapParticipantes->setDado('cod_licitacao' ,$rsLic->getCampo('cod_licitacao'));
        $obMapParticipantes->setDado('cod_entidade'  ,$rsLic->getCampo('cod_entidade'));
        $obMapParticipantes->setDado('cod_modalidade',$rsLic->getCampo('cod_modalidade'));
        $obMapParticipantes->setDado('exercicio',$rsLic->getCampo('exercicio'));
        $obMapParticipantes->recuperaParticipantes($rsParticipantes);

        $i=0;
        $arPart = array();
        while (!$rsParticipantes->eof()) {
            $arPart[$i] = array();
            $arPart[$i]['cgmParticipante'] = $rsParticipantes->getCampo('cgm_fornecedor');
            $arPart[$i]['stNomParticipante'] = $rsParticipantes->getCampo('fornecedor');
            $arPart[$i]['cgmRepLegal'] = $rsParticipantes->getCampo('numcgm_representante');
            $arPart[$i]['stNomRepLegal'] = $rsParticipantes->getCampo('representante');

            $numCgmConsorcio = $rsParticipantes->getCampo('cgm_consorcio');
            if (!empty($numCgmConsorcio)) {
                //se o tipo do participante for consorcio, entao le/seta os dados do consorcio
                $arPart[$i]['cgmConsorcio'] = $numCgmConsorcio;
                $arPart[$i]['stNomConsorcio'] = $rsParticipantes->getCampo('consorcio');
                $arPart[$i]['tipoParticipacao'] = 'consorcio';
            } else {
                $arPart[$i]['tipoParticipacao'] = 'isolado';
            }

            $arPart[$i]['dataInclusao'] = date("d/m/Y", strtotime ($rsParticipantes->getCampo('dt_inclusao')));
            $arPart[$i]['numOrdem'] = $i;
            $rsParticipantes->proximo();
            $i++;
        }

        Sessao::write('part', $arPart);
        $stJs .= montaSpanParticipante();

        $stJs.= imprimeListaParticipantesLicitacao( $arPart );
    break;

    case 'excluirParticipante':
        //basicamente percorre o array de participantes em sessao,
        //colocando os registros q devem ser mantidos num outro array, atualizando o numero de ordem
        $arTemp = array();
        $novoNumOrdem=0;
        $arPart = Sessao::read('part');
        $arPartCount = count($arPart);
        for ($i=0; $i<$arPartCount; $i++) {
            $item = $arPart[$i];
            if ($item['numOrdem']!=(int) $_REQUEST['numOrdem']) {
                $item['numOrdem']=$novoNumOrdem;
                $arTemp[]=$item;
                $novoNumOrdem++;
            }
        }

        Sessao::write('part', $arTemp);
        $stJs .= imprimeListaParticipantesLicitacao($arTemp);
  break;

  case "limpar":
        Sessao::write('part', array());
  break;
}

echo $stJs;

?>
