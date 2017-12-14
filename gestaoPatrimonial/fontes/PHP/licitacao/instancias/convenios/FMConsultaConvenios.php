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
 * Consulta de Convenio
 * Data de Criação   : 03/10/2006

 * @author Analista:
 * @author Desenvolvedor:  Lucas Teixeira Stephanou
 * @ignore
 * Casos de uso: uc-03.05.14

 $Id: FMConsultaConvenios.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "consultar";
}

//Define o nome dos arquivos PHP
$stPrograma = "ManterConvenios";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once $pgJs;
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/URBEM/ajax.php';

// limpar sessao de veiculos
Sessao::remove('nuValorAtual');
Sessao::remove('boAlteracao');
Sessao::remove('participantes');
Sessao::remove('rsVeiculos');

$boAlterar = true;

require_once CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoConvenio.class.php';
require_once CAM_GP_LIC_MAPEAMENTO . 'TLicitacaoRescisaoConvenio.class.php';
$obConvenio = new TLicitacaoConvenio;

$inNumConvenio = $_REQUEST[ 'inNumConvenio' ];

//virifica Convenios rescindidos.
$obTLicitacaoRescisaoConvenio = new TLicitacaoRescisaoConvenio();
$obTLicitacaoRescisaoConvenio->setDado("num_convenio", $inNumConvenio);
$obTLicitacaoRescisaoConvenio->setDado("exercicio_convenio", Sessao::read('exercicio'));
$obTLicitacaoRescisaoConvenio->recuperaMontaRecuperaDadosRescisao($rsRescisaoConvenio);

$stFiltro = ' AND convenio.num_convenio = ' . $inNumConvenio . ' ' ;

$obConvenio->recuperaRelacionamento ( $rsConvenio , $stFiltro , ' convenio.num_convenio');

$obLblNumConvenio = new Label;
$obLblNumConvenio->setRotulo ( 'Número do Convênio' );
$obLblNumConvenio->setValue  ( $rsConvenio->getCampo( 'num_convenio' ) );

$obLblTipoConvenio = new Label;
$obLblTipoConvenio->setRotulo ( 'Tipo de Convênio' );
$obLblTipoConvenio->setValue  (  $rsConvenio->getCampo( 'descricao_tipo' ) );

$obLblObjeto = new Label;
$obLblObjeto->setRotulo ( 'Objeto' );
$obLblObjeto->setValue  ( $rsConvenio->getCampo( 'descricao_objeto' ) );

$obLblObs = new Label;
$obLblObs->setRotulo ( 'Observações' );
$obLblObs->setValue  ( nl2br( str_replace( ' ' , '&nbsp;' ,$rsConvenio->getCampo( 'observacao' ) ) ) );

$obLblRespJur = new Label;
$obLblRespJur->setRotulo ( 'Respónsavel Jurídico' );
$obLblRespJur->setValue  ( $rsConvenio->getCampo( 'numcgm' ) . " - " .$rsConvenio->getCampo( 'nom_cgm' ) );

list($ano,$mes,$dia) = explode ( '-' , $rsConvenio->getCampo( 'dt_assinatura' )) ;
$obLblDtAssinatura = new Label;
$obLblDtAssinatura->setRotulo ( 'Data da Assinatura' );
$obLblDtAssinatura->setValue  ( $dia.'/'.$mes.'/'.$ano );

unset ( $ano,$mes,$dia) ;
list($ano,$mes,$dia) = explode ( '-' , $rsConvenio->getCampo( 'dt_assinatura' )) ;
$obLblDtVigencia = new Label;
$obLblDtVigencia->setRotulo ( 'Data do Final da Vigência' );
$obLblDtVigencia->setValue  (  $dia.'/'.$mes.'/'.$ano  );

$obLblValor = new Label;
$obLblValor->setRotulo ( 'Valor do Convênio' );
$obLblValor->setValue  ( number_format ( $rsConvenio->getCampo( 'valor' ) , 2 , ',' , '.' ) );

$stSituacao = $rsConvenio->getCampo( 'situacao' );
//$stSituacao = $stSituacao == 'Anulado' ? '<font color=red>Anulado</font>' : $stSituacao;
$obLblSituacao = new Label;
$obLblSituacao->setRotulo ( 'Situação Atual' );

if (count($rsRescisaoConvenio->arElementos) > 0) {
    $obLblSituacao->setValue  ( 'Rescindido' );
} else {
    $obLblSituacao->setValue  ( $stSituacao );
}

$obLblDocumento = new Label;
$obLblDocumento->setRotulo ( 'Documento' );
$obLblDocumento->setValue  ( $rsConvenio->getCampo('cod_documento')." - ".$rsConvenio->getCampo('descricao'));

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda     ( "UC-03.05.14" );
$obFormulario->addTitulo    ( "Consulta de Convênio");
$obFormulario->addComponente( $obLblNumConvenio );
$obFormulario->addComponente( $obLblTipoConvenio);
$obFormulario->addComponente( $obLblObjeto      );
$obFormulario->addComponente( $obLblObs         );
$obFormulario->addComponente( $obLblRespJur     );
$obFormulario->addComponente( $obLblDtAssinatura);
$obFormulario->addComponente( $obLblDtVigencia  );
$obFormulario->addComponente( $obLblValor       );
$obFormulario->addComponente( $obLblSituacao    );
$obFormulario->addComponente( $obLblDocumento   );
$obFormulario->show();

/* LISTAS */

// carrega mapeamentos
require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoPublicacaoConvenio.class.php" );
require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoParticipanteConvenio.class.php" );
require_once ( CAM_GP_LIC_MAPEAMENTO . "TLicitacaoTipoParticipante.class.php" );
require_once ( CAM_GA_CGM_NEGOCIO . "RCGM.class.php");
// cria instancia de cgm
$obCgm = new RCGM;
$obTLicitacaoPublicacaoConvenio = new TLicitacaoPublicacaoConvenio;
$obTLicitacaoPublicacaoConvenio->setDado( 'num_convenio' , $_REQUEST['inNumConvenio']);
$obTLicitacaoPublicacaoConvenio->setDado( 'exercicio'    , Sessao::getExercicio());
$obTLicitacaoPublicacaoConvenio->recuperaVeiculosPublicacao( $rsVeiculos );

$obListaVeiculos = new Lista;
$obListaVeiculos->setRecordSet ( $rsVeiculos );
$obListaVeiculos->setTitulo ( "Veículos de Publicidade " );
$obListaVeiculos->setMostraPaginacao ( false );

$obListaVeiculos->addCabecalho ();
$obListaVeiculos->ultimoCabecalho->addConteudo ( "&nbsp;" );
$obListaVeiculos->ultimoCabecalho->setWidth    ( 5 );
$obListaVeiculos->commitCabecalho ();

$obListaVeiculos->addCabecalho ();
$obListaVeiculos->ultimoCabecalho->addConteudo ( "Nome do Veículo de Publicidade" );
$obListaVeiculos->ultimoCabecalho->setWidth ( 60 );
$obListaVeiculos->commitCabecalho ();

$obListaVeiculos->addDado ();
$obListaVeiculos->ultimoDado->setCampo ( "nom_veiculo" );
$obListaVeiculos->commitDado ();

$obListaVeiculos->show();

// LISTA DE PARTICIPANTES

$obParConvenio = new TLicitacaoParticipanteConvenio;
$stFiltro = " WHERE num_convenio = " . $inNumConvenio;
$obParConvenio->recuperaRelacionamento( $rsParticipantes , $stFiltro , '' );

$obTipoParticipante = new TLicitacaoTipoParticipante;
$arParticipantes = array();
// carrega participantes
while ( !$rsParticipantes->eof() ) {
    $obCgm->setNumCGM ( $rsParticipantes->getCampo( 'cgm_fornecedor' ) );
    $obCgm->consultar ( new Recordset );
    $stNomCgm = $obCgm->getNomCGM();

    $obTipoParticipante->setDado ( 'cod_tipo_participante' , $rsParticipantes->getCampo ( 'cod_tipo_participante' ) );
    $obTipoParticipante->recuperaPorChave ( $rsTipoParticipante );

    $participacao = number_format( $rsParticipantes->getCampo( 'percentual_participacao' ) , 2 , ',' , '.');
    $participacao .=  " % ";

    $arParticipantes[]  = array  (
                        'inCgmParticipante'         => $rsParticipantes->getCampo( 'cgm_fornecedor' ) ,
                        'inCodTipoParticipante'     => $rsParticipantes->getCampo ( 'cod_tipo_participante' ) ,
                        'stNomCgmParticipante'      => $stNomCgm ,
                        'descricao_participacao'    => $rsTipoParticipante->getCampo ( 'descricao' ) ,
                        'nuValorParticipacao'       => $rsParticipantes->getCampo( 'valor_participacao' ),
                        'nuPercentualParticipacao'  => $participacao,
                        'hdnPercentualParticipacao' => $rsParticipantes->getCampo( 'percentual_participacao' )
                    );

    $rsParticipantes->proximo();
}
$rsParticipantes = new Recordset;
$rsParticipantes->preenche ( $arParticipantes );

$rsParticipantes->addFormatacao ( 'nuValorParticipacao' , 'NUMERIC_BR' ) ;
$rsParticipantes->ordena ( 'nuPercentualParticipacao' ) ;
$rsParticipantes->setPrimeiroElemento();

$obListaParticipantes = new Lista;
$obListaParticipantes->setRecordSet ( $rsParticipantes );
$obListaParticipantes->setTitulo ( "Participantes do Convênio " );
$obListaParticipantes->setMostraPaginacao ( false );

$obListaParticipantes->addCabecalho ();
$obListaParticipantes->ultimoCabecalho->addConteudo ( "&nbsp;" );
$obListaParticipantes->ultimoCabecalho->setWidth    ( 5 );
$obListaParticipantes->commitCabecalho ();

$obListaParticipantes->addCabecalho ();
$obListaParticipantes->ultimoCabecalho->addConteudo ( "Nome" );
$obListaParticipantes->ultimoCabecalho->setWidth ( 60 );
$obListaParticipantes->commitCabecalho ();

$obListaParticipantes->addCabecalho ();
$obListaParticipantes->ultimoCabecalho->addConteudo ( "Tipo de Participação" );
$obListaParticipantes->ultimoCabecalho->setWidth ( 60 );
$obListaParticipantes->commitCabecalho ();

$obListaParticipantes->addCabecalho ();
$obListaParticipantes->ultimoCabecalho->addConteudo ( "Valor Participação" );
$obListaParticipantes->ultimoCabecalho->setWidth ( 60 );
$obListaParticipantes->commitCabecalho ();

$obListaParticipantes->addCabecalho ();
$obListaParticipantes->ultimoCabecalho->addConteudo ( "Participação " );
$obListaParticipantes->ultimoCabecalho->setWidth ( 60 );
$obListaParticipantes->commitCabecalho ();

$obListaParticipantes->addDado ();
$obListaParticipantes->ultimoDado->setCampo ( "stNomCgmParticipante" );
$obListaParticipantes->commitDado ();

$obListaParticipantes->addDado ();
$obListaParticipantes->ultimoDado->setCampo ( "descricao_participacao" );
$obListaParticipantes->commitDado ();

$obListaParticipantes->addDado ();
$obListaParticipantes->ultimoDado->setAlinhamento ( "CENTRO" );
$obListaParticipantes->ultimoDado->setCampo ( "nuValorParticipacao" );
$obListaParticipantes->commitDado ();

$obListaParticipantes->addDado ();
$obListaParticipantes->ultimoDado->setAlinhamento ( "CENTRO" );
$obListaParticipantes->ultimoDado->setCampo ( "[nuPercentualParticipacao] " );
$obListaParticipantes->commitDado ();

$obListaParticipantes->show();

/* VOLTAR */

$obBtnVoltar = new Button;
$obBtnVoltar->setName              ( "btnVoltar" );
$obBtnVoltar->setValue             ( "Voltar" );
$obBtnVoltar->setTipo              ( "button" );
$obBtnVoltar->obEvento->setOnClick ( "consultaVoltar()" );
$obBtnVoltar->setDisabled          ( false );

$botoes = array ( $obBtnVoltar );

$obFormulario = new Formulario;
$obFormulario->defineBarra  ( $botoes, 'left', '' );
$obFormulario->show();
