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

    * Página de Formulário para manter participantes
    * Data de Criação   : 06/10/2006

    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Maicon Brauwers

    * @ignore

    * $Id: OCManterManutencaoParticipante.php 57478 2014-03-11 20:29:45Z jean $

    * Casos de uso : uc-03.05.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(TLIC."TLicitacaoLicitacao.class.php");
include_once(TLIC."TLicitacaoEdital.class.php");
include_once(TLIC."TLicitacaoParticipante.class.php");
include_once(TLIC."TLicitacaoParticipanteConsorcio.class.php");
include_once(CAM_GP_COM_MAPEAMENTO."TComprasObjeto.class.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterRenunciaRecurso";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

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
 * Imprime a lista dos participantes da licitacao
 */
function imprimeListaParticipantesLicitacao($arLista, $boExecuta = true)
{
    
    $rs = new RecordSet();
    $rs->preenche($arLista);
        
    //cria o objeto de interface de listagem
    $obTable = new Table;
    $obTable->setRecordSet( $rs );
    
    $obTable->setSummary('Participantes da Licitação ');
    
    //imprime o cabecalho
    $obTable->Head->addCabecalho( 'Participante'                , 72 );
    $obTable->Head->addCabecalho( 'Renúncia ao Prazo de Recurso', 10 );
    
    //imprime o corpo
    $obTable->Body->addCampo( 'stNomParticipante'  , 'E' );
    
    $obChkPermissao = new CheckBox;
    $obChkPermissao->setName('boPermissao_[cgmParticipante]');
    $obChkPermissao->setId('boPermissao_[cgmParticipante]');
    
    $obTable->Body->addComponente($obChkPermissao, 'ok');
    
    $obTable->montaHTML();
    $stHTML = $obTable->getHtml();
    $stHTML = str_replace("\n","",$stHTML);
    $stHTML = str_replace("  ","",$stHTML);
    $stHTML = str_replace("'","\\'",$stHTML);
    
    $stJs = "jq('#spnListaParticipantesLic').html('". $stHTML ."'); ";
    
    $rs->setPrimeiroElemento();
    
    while (!$rs->eof()) {
        if ( $rs->getCampo('boRenunciaRecurso') == 't' ) {
          $stJs .= "$('boPermissao_".$rs->getCampo('cgmParticipante')."').checked = true;";
        } else {
          $stJs .= "$('boPermissao_".$rs->getCampo('cgmParticipante')."').checked = false;";
        }
      $rs->proximo();
    }
    
    return $stJs;
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

switch ($stCtrl) {
    
    case 'exibeLicitacao':
        
        //le o objeto da licitacao
        //e monta a lista de participantes ja cadastrados
        if (empty($_REQUEST['numEdital'])) {
            //o numero da licitazao ta zerado, limpa tudo
            $stJs .= "document.getElementById('objetoLicitatorio').innerHTML = ''; ";
            $stJs .= "document.getElementById('spnEdital').innerHTML = ''; ";
            $stJs .= "document.getElementById('spnListaParticipantesLic').innerHTML = ''; ";
            $stJs .= "document.getElementById('spnParticipante').innerHTML = ''; ";
            $stJs .= "document.getElementById('numEdital').focus();";
            break;
        }
        
        $arEdital = explode('/', $_REQUEST['numEdital'] );
        $numEdital       = $arEdital[0];
        $exercicioEdital = $arEdital[1];
        
        if (empty($numEdital) || (int) $numEdital==0) {
            $stJs .= "document.getElementById('numEdital').value = '';";
            $stJs .= "document.getElementById('numEdital').focus();";
            $stJs .= "alertaAviso('O Número do Edital deve ser preenchido!', 'form','erro','".Sessao::getId()."');";
            break;
        }
        
        if ( empty($exercicioEdital) ) { $exercicioEdital = Sessao::getExercicio(); }
        
        //O usuario digitou algo, agora busca no BD
        $objEdital = new TLicitacaoEdital;
        $objEdital->setDado("num_edital",$numEdital);
        $objEdital->setDado("exercicio_licitacao",$exercicioEdital );
        $stFiltro = "   AND NOT EXISTS  (
                                        SELECT  1
                                          FROM  licitacao.edital_anulado
                                         WHERE  edital_anulado.num_edital = edital.num_edital
                                           AND  edital_anulado.exercicio = edital.exercicio
                                        )";
        $objEdital->recuperaLicitacao($rsLic,$stFiltro);
        
        //licitacao nao encontrada
        if ($rsLic->eof()) {
            //Aviso de que o edital nao existe
            $stJs .= "alertaAviso('Edital ".$numEdital."/".$exercicioEdital." não encontrado!', 'form','erro','".Sessao::getId()."');";
            //Limpa tudo
//            $stJs .= "document.getElementById('objetoLicitatorio').innerHTML = ''; ";
//            $stJs .= "document.getElementById('spnEdital').innerHTML = ''; ";
//            $stJs .= "document.getElementById('spnListaParticipantesLic').innerHTML = ''; ";
//            $stJs .= "document.getElementById('spnParticipante').innerHTML = ''; ";
//            $stJs .= "document.getElementById('numEdital').value = '';";
//            $stJs .= "document.getElementById('numEdital').focus();";
            break;
        }
        
        $obTLicitacaoLicitacao = new TLicitacaoLicitacao;
        $obTLicitacaoLicitacao->setDado('exercicio'      , $rsLic->getCampo('exercicio')      );
        $obTLicitacaoLicitacao->setDado('cod_licitacao'  , $rsLic->getCampo('cod_licitacao')  );
        $obTLicitacaoLicitacao->setDado('cod_modalidade' , $rsLic->getCampo('cod_modalidade') );
        $obTLicitacaoLicitacao->setDado('cod_entidade'   , $rsLic->getCampo('cod_entidade')   );
        
        if ($obErro) {
            # Controles para limpar o form.
            $stJs .= "document.getElementById('objetoLicitatorio').innerHTML = ''; ";
            $stJs .= "document.getElementById('spnEdital').innerHTML = ''; ";
            $stJs .= "document.getElementById('spnListaParticipantesLic').innerHTML = ''; ";
            $stJs .= "document.getElementById('spnParticipante').innerHTML = ''; ";
            $stJs .= "document.getElementById('numEdital').value = '';";
            $stJs .= "document.getElementById('numEdital').focus();";
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
        $objetoLicitatorio = $rsLic->getCampo('cod_licitacao')."-".$rsLic->getCampo('cod_modalidade')."-".$rsLic->getCampo('cod_entidade')."-".$rsLic->getCampo('exercicio')."-".nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsObjLic->getCampo("descricao"))));
        
        //seta o valor no span
        include_once ( CAM_GP_LIC_COMPONENTES. "ILabelNumeroLicitacao.class.php" );
        $obForm = new Form;
        
        $stJs.= "document.getElementById('objetoLicitatorio').innerHTML = '".$objetoLicitatorio."';";
        
        $obLblNumeroLicitacao = new ILabelNumeroLicitacao( $obForm );
        $obLblNumeroLicitacao->setExercicio     ( $rsLic->getCampo('exercicio') );
        $obLblNumeroLicitacao->setNumEdital     ( $numEdital );
        $obLblNumeroLicitacao->setCodEntidade   ( Sessao::read('cod_entidade'));
        $obLblNumeroLicitacao->setNumLicitacao  ( Sessao::read('cod_licitacao'));
        $obLblNumeroLicitacao->setMostrarObjeto ( true );
        $obLblNumeroLicitacao->setMostrarDataHoraLic ( false );
        $obLblNumeroLicitacao->setBoFiltro      ( "false" );
                
        $obFormulario = new Formulario($obForm);
        $obLblNumeroLicitacao->geraFormulario( $obFormulario );
        $obFormulario->montaInnerHTML();
        
        $stJs .= "document.getElementById('numEdital').value = '".$numEdital."/".$exercicioEdital."';";
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
            $arPart[$i]['boRenunciaRecurso'] = $rsParticipantes->getCampo('renuncia_recurso');
            
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
        $stJs.= imprimeListaParticipantesLicitacao( $arPart );
    break;
        
    case "limpar":
        Sessao::write('part', array());
    break;
}

echo $stJs;

?>
