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
    * Página para Consulta de Imóvel
    * Data de Criação: 13/06/2004

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    * $Id: FMConsultaImovel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.18
*/

/*
$Log$
Revision 1.15  2007/03/08 21:14:51  cassiano
Bug #5100#

Revision 1.14  2006/09/18 10:30:20  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteRural.class.php"        );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLoteUrbano.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"           );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php" );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"       );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ConsultaImovel";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?";//.Sessao::getId().$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgOculFoto = "OC".$stPrograma."Imagem.php";
$pgOculCons = "OC".$stPrograma."Construcao.php";
$pgJs       = "JS".$stPrograma.".js";

include_once( $pgJs       );
include_once( $pgOcul     );
include_once( $pgOculCons );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$stFiltro = '';
$arTransf4 = Sessao::read('sessao_transf4');

if ($arTransf4) {
    $stFiltro = '';
    foreach ($arTransf4 as $stCampo => $stValor) {
        if ( is_array($stValor) ) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".@urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

Sessao::remove( 'proprietarios' );
Sessao::remove( 'promitentes'   );

$obRCIMImovel = new RCIMImovel( new RCIMLote );
$obRCIMImovel->obRCIMConfiguracao->setCodigoModulo( 12 );
$obRCIMImovel->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCIMImovel->obRCIMConfiguracao->consultarConfiguracao();
$stMascaraIM = $obRCIMImovel->obRCIMConfiguracao->getMascaraIM();
$obRCIMImovel->obRCIMConfiguracao->consultarMascaraProcesso( $stMascaraProcesso );

$obRCIMImovel->roRCIMLote->setCodigoLote( $_REQUEST["inCodLote"] );
$obRCIMImovel->roRCIMLote->consultarLote( "", FALSE );

//RECUPERA A LISTA DE ENDEREÇOS
$obRCIMConfrontacaoTrecho = new RCIMConfrontacaoTrecho( $obRCIMImovel->roRCIMLote );
$obRCIMConfrontacaoTrecho->setPrincipal( "t" );
$obRCIMConfrontacaoTrecho->listarConfrontacoes( $rsListaConfrontacoes );
$rsListaConfrontacoes->ordena("cod_confrontacao");

$obRCIMImovel->setNumeroInscricao                 ( $_REQUEST["inCodInscricao"] );
$obRCIMImovel->obRCIMImobiliaria->setRegistroCreci( $_REQUEST["stCreci"]        );
$obErro = $obRCIMImovel->consultarImovel();

//RECUPERA ENDERECOS DE ENTREGA
$obRCIMImovel->listarEnderecoEntrega( $rsListaEnderecoEntrega );

//DEFINICAO DOS ATRIBUTOS DE IMOVEL
$arChaveAtributoImovel =  array( "inscricao_municipal" => $_REQUEST["inCodInscricao"] );
$obRCIMImovel->obRCadastroDinamico->setChavePersistenteValores          ( $arChaveAtributoImovel );
$obRCIMImovel->obRCadastroDinamico->consultaAtributosSelecionadosValores( $rsAtributosImovel     );

// recebe varaiveis de timestamp do processo(se haver) para comparação
// se timestamp do processo for igual a timestamp do imovel,
// este processo é o de inclusao, se nao for igual
// liberar campo de processo e inseri-lo com o mesmo timestamp do imovel
$rsListaProcesso = new Recordset;
$obRCIMImovel->listarProcessos( $rsListaProcesso );
$tmTimestampProcesso = $rsListaProcesso->getCampo("timestamp")  ;
$tmTimestampImovel   = $obRCIMImovel->getTimestampImovel()  ;
$boTimestampIgual    = $tmTimestampProcesso == $tmTimestampImovel;
if ($boTimestampIgual) {
    $stProcesso = $rsListaProcesso->getCampo( "cod_processo_ano" );
}
// fim das variaveis de timestamp

$obMontaAtributosImovel = new MontaAtributos;
$obMontaAtributosImovel->setTitulo     ( "Atributos"        );
$obMontaAtributosImovel->setName       ( "Atributo_"        );
$obMontaAtributosImovel->setLabel      ( true               );
$obMontaAtributosImovel->setRecordSet  ( $rsAtributosImovel );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//COMPONENTES PARA A ABA INSCRICAO IMOBILIARIA
$obLblNumeroInscricao = new Label;
$obLblNumeroInscricao->setRotulo( "Número da Inscrição"               );
$obLblNumeroInscricao->setValue ( $obRCIMImovel->getNumeroInscricao() );

$obLblMatricula = new Label;
$obLblMatricula->setRotulo      ( "Matrícula no Registro de Imóveis"           );
$obLblMatricula->setValue       ( $obRCIMImovel->getMatriculaRegistroImoveis() );

$obLblDataInscricao = new Label;
$obLblDataInscricao->setRotulo  ( "Data da Inscrição"               );
$obLblDataInscricao->setValue   ( $obRCIMImovel->getDataInscricao() );

$obLblCondominio = new Label;
$obLblCondominio->setRotulo     ( "Condomínio" );
$obLblCondominio->setValue      ( $obRCIMImovel->obRCIMCondominio->getNomCondominio() );

$obLblEndereco = new Label;
$obLblEndereco->setRotulo       ( "Logradouro" );
$obLblEndereco->setValue        ( $obRCIMImovel->getLogradouro() );

$obLblNrComplemento = new Label;
$obLblNrComplemento->setRotulo  ( "Número / Complemento" );
$obLblNrComplemento->setValue   ( $obRCIMImovel->getNumeroImovel()." / ".$obRCIMImovel->getComplementoImovel() );

$obLblBairro = new Label;
$obLblBairro->setRotulo         ( "Bairro" );
$obLblBairro->setValue          ( $obRCIMImovel->roRCIMLote->obRCIMBairro->getNomeBairro() );

$obLblFracaoIdeal = new Label;
$obLblFracaoIdeal->setRotulo     ( "Fração Ideal do Lote" );
$obLblFracaoIdeal->setValue      ( $obRCIMImovel->getFracaoIdeal() );

$obLblAreaEdificada = new Label;
$obLblAreaEdificada->setRotulo  ( "Área Total Edificada" );
$obLblAreaEdificada->setValue   ( $obRCIMImovel->getAreaEdificada() );

$obLblAreaEdificadaLote = new Label;
$obLblAreaEdificadaLote->setRotulo  ( "Área Edificada do Lote" );
$obLblAreaEdificadaLote->setValue   ( $obRCIMImovel->roRCIMLote->getAreaEdificadaLote() );

$obLblCreci = new Label;
$obLblCreci->setRotulo          ( "CRECI" );
$obLblCreci->setValue           ( $obRCIMImovel->obRCIMImobiliaria->getRegistroCreci() );

$obLblProcesso = new Label;
$obLblProcesso->setRotulo       ( "Processo" );
$obLblProcesso->setValue        ( isset($stProcesso) ? $stProcesso : "");

$stSituacao = 'Ativo';
if ( $obRCIMImovel->getDataBaixa() && $obRCIMImovel->getDataTermino() == "" ) {
    $stSituacao = 'Baixado';
}

$obLblSituacao = new Label;
$obLblSituacao->setRotulo       ( "Situação"  );
$obLblSituacao->setValue        ( $stSituacao );

$obLblDtBaixa = new Label;
$obLblDtBaixa->setRotulo        ( "Data de Baixa" );
$obLblDtBaixa->setValue         ( $obRCIMImovel->getDataBaixa() );

$obLblMotivo = new Label;
$obLblMotivo->setRotulo         ( "Motivo" );
$obLblMotivo->setValue          ( $obRCIMImovel->getJustificativa() );

//COMPONENTES DO ENDERECO DE ENTREGA
$obLblLogradouroEntrega = new Label;
$obLblLogradouroEntrega->setRotulo ( "Logradouro" );
$obLblLogradouroEntrega->setValue  ( $obRCIMImovel->getEnderecoEntrega() );

$obLblNumeroEntrega = new Label;
$obLblNumeroEntrega->setRotulo     ( "Número" );
$obLblNumeroEntrega->setValue      ( $obRCIMImovel->getNumeroEntrega() );

$obLblComplementoEntrega = new Label;
$obLblComplementoEntrega->setRotulo( "Complemento" );
$obLblComplementoEntrega->setValue ( $obRCIMImovel->getComplementoEntrega() );

$obLblCEPEntrega = new Label;
$obLblCEPEntrega->setRotulo        ( "CEP" );
$obLblCEPEntrega->setValue         ( $obRCIMImovel->getCEPEntrega() );

$obLblBairroEntrega = new Label;
$obLblBairroEntrega->setRotulo     ( "Bairro" );
$obLblBairroEntrega->setValue      ( $obRCIMImovel->getBairroEntrega() );

$obLblCxPostalEntrega = new Label;
$obLblCxPostalEntrega->setRotulo   ( "Caixa Postal" );
$obLblCxPostalEntrega->setValue    ( $obRCIMImovel->getCaixaPostal() );

$obLblMunicipioEntrega = new Label;
$obLblMunicipioEntrega->setRotulo  ( "Município" );
$obLblMunicipioEntrega->setValue   ( $obRCIMImovel->getMunicipioEntrega() );

$obLblUFEntrega = new Label;
$obLblUFEntrega->setRotulo         ( "Estado" );
$obLblUFEntrega->setValue          ( $obRCIMImovel->getUFEntrega() );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

//DEFINICAO DO FORMULARIO

$obFormulario = new FormularioAbas;
$obFormulario->addForm      ( $obForm                   );
$obFormulario->setAjuda ( "UC-05.01.18" );
$obFormulario->addHidden    ( $obHdnCtrl                );

//ABA -> IMOVEL
$obFormulario->addAba       ( "Inscrição Imobiliária"   );
$obFormulario->addTitulo    ( "Dados do imóvel"         );
$obFormulario->addComponente( $obLblNumeroInscricao     );
$obFormulario->addComponente( $obLblMatricula           );
$obFormulario->addComponente( $obLblDataInscricao       );
$obFormulario->addComponente( $obLblCondominio          );
$obFormulario->addComponente( $obLblEndereco            );
$obFormulario->addComponente( $obLblNrComplemento       );
$obFormulario->addComponente( $obLblBairro              );
$obFormulario->addComponente( $obLblAreaEdificada       );
$obFormulario->addComponente( $obLblAreaEdificadaLote   );
$obFormulario->addComponente( $obLblFracaoIdeal         );
$obFormulario->addComponente( $obLblCreci               );
$obFormulario->addComponente( $obLblProcesso            );
$obFormulario->addComponente( $obLblSituacao            );
if ($stSituacao == 'Baixado') {
    $obFormulario->addComponente( $obLblDtBaixa         );
    $obFormulario->addComponente( $obLblMotivo          );
}

if ( $obRCIMImovel->getEnderecoEntrega() AND $obRCIMImovel->getUFEntrega() AND $obRCIMImovel->getMunicipioEntrega() ) {
    $obFormulario->addTitulo    ( "Endereço de Entrega Atual");
    $obFormulario->addComponente( $obLblLogradouroEntrega    );
    $obFormulario->addComponente( $obLblNumeroEntrega        );
    $obFormulario->addComponente( $obLblComplementoEntrega   );
    $obFormulario->addComponente( $obLblCEPEntrega           );
    $obFormulario->addComponente( $obLblBairroEntrega        );
    $obFormulario->addComponente( $obLblCxPostalEntrega      );
    $obFormulario->addComponente( $obLblMunicipioEntrega     );
    $obFormulario->addComponente( $obLblUFEntrega            );

    include_once 'FMConsultaImovelListaEnderecoEntrega.php';
    $obFormulario->addSpan      ( $obSpnEnderecoEntrega     );
}

$obMontaAtributosImovel->geraFormulario ( $obFormulario );
include_once 'FMConsultaImovelListaProcessos.php';
$obFormulario->addSpan      ( $obSpnProcesso            );
$obFormulario->addSpan      ( $obSpnAtributosProcesso   );
//FIM ABA -> IMOVEL

//ABA -> EDIFICACOES
$obFormulario->addAba( "Edificações"                    );
include_once 'FMConsultaImovelEdificacoes.php';
$obFormulario->addSpan      ( $obSpnListaEdificacoes    );
$obFormulario->addSpan      ( $obSpnEdificacao          );
//FIM ABA -> EDIFICACOES

//ABA -> CONSTRUCOES
$obFormulario->addAba       ( "Construções"             );
include_once 'FMConsultaImovelConstrucoes.php';
$obFormulario->addSpan      ( $obSpnListaConstrucoes    );
$obFormulario->addSpan      ( $obSpnConstrucao          );
//FIM ABA -> CONSTRUCOES

//ABA -> IMAGENS
$obFormulario->addAba       ( "Imagens"             );
include_once 'FMConsultaImovelImagem.php';
$obFormulario->addComponente($obImageBox);
//FIM ABA -> IMAGENS

$stLocation = $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.$stFiltro.'&inCodInscricao=10';

$obButtonVoltar = new Button;
$obButtonVoltar->setName  ( "Voltar" );
$obButtonVoltar->setValue ( "Voltar" );
$obButtonVoltar->obEvento->setOnClick( "Cancelar('".$stLocation."');" );
$obFormulario->defineBarra( array( $obButtonVoltar ), "left", "" );

$obFormulario->show();
?>
