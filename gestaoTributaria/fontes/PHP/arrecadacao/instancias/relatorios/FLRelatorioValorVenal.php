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
/*
 * Tela de Filtro para geração do relatório
 * Data de Criação   : 26/11/2008

 * @author Analista      Sabrina Moreira
 * @author Desenvolvedor Alexandre Melo

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php"                                     );

//MONTA MASCARA DE LOCALIZACAO
$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->obRCIMLocalizacao->recuperaVigenciaAtual( $rsVigencia );
$obMontaLocalizacao->obRCIMLocalizacao->setCodigoVigencia( $rsVigencia->getCampo( 'cod_vigencia' ));
$obMontaLocalizacao->obRCIMLocalizacao->listarNiveis( $rsRecordSet );
while ( !$rsRecordSet->eof() ) {
    $obMontaLocalizacao->stMascara .= $rsRecordSet->getCampo("mascara").".";
    $rsRecordSet->proximo();
}
$stMascaraLocalizacao = substr( $obMontaLocalizacao->getMascara(), 0 , strlen($obMontaLocalizacao->getMascara()) - 1 );

$obForm = new Form;
$obForm->setAction               ( "OCRelatorioValorVenal.php"               );
$obForm->setTarget               ( "oculto"                                  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName              ( "stAcao"                                  );
$obHdnAcao->setValue             ( $_REQUEST['stAcao']                       );

//LOCALIZAÇÃO
$obCodInicioLocalizacao = new TextBox;
$obCodInicioLocalizacao->setName                    ( "inCodInicioLocalizacao"                );
$obCodInicioLocalizacao->setId                      ( "inCodInicioLocalizacao"                );
$obCodInicioLocalizacao->setRotulo                  ( "Localização"                           );
$obCodInicioLocalizacao->obEvento->setOnKeyUp       ( "mascaraDinamico('".$stMascaraLocalizacao."', this, event);" );
$obCodInicioLocalizacao->setSize                    ( strlen($stMascaraLocalizacao)+2         );
$obCodInicioLocalizacao->setMaxLength               ( strlen($stMascaraLocalizacao)+2         );
$obCodInicioLocalizacao->setTitle                   ( "Informe um período"                    );

$obLblPeriodoLocalizacao = new Label;
$obLblPeriodoLocalizacao->setValue                  ( " até " );

$obCodTerminoLocalizacao = new TextBox;
$obCodTerminoLocalizacao->setName                   ( "inCodTerminoLocalizacao"               );
$obCodTerminoLocalizacao->setRotulo                 ( "Localização"                           );
$obCodTerminoLocalizacao->obEvento->setOnKeyUp      ( "mascaraDinamico('".$stMascaraLocalizacao."', this, event);" );
$obCodTerminoLocalizacao->setSize                   ( strlen($stMascaraLocalizacao)+2         );
$obCodTerminoLocalizacao->setMaxLength              ( strlen($stMascaraLocalizacao)+2         );
$obCodTerminoLocalizacao->setTitle                  ( "Informe um período"                    );

//LOTE
$obBscLoteInicio = new TextBox;
$obBscLoteInicio->setRotulo                         ( "Lote"                                  );
$obBscLoteInicio->setTitle                          ( "Intervalo"                             );
$obBscLoteInicio->setName                           ( "inCodLoteInicio"                       );
$obBscLoteInicio->setValue                          ( $inCodLoteInicio                        );
$obBscLoteInicio->setSize                           ( 10                                      );
$obBscLoteInicio->setMaxLength                      ( 10                                      );
$obBscLoteInicio->setNull                           ( true                                    );
$obBscLoteInicio->setInteiro                        ( true                                    );

$obLblPeriodoLote = new Label;
$obLblPeriodoLote->setValue                         ( " até " );

$obBscLoteFinal = new TextBox;
$obBscLoteFinal->setRotulo                          ( "Lote"                                  );
$obBscLoteFinal->setTitle                           ( "Intervalo"                             );
$obBscLoteFinal->setName                            ( "inCodLoteFinal"                        );
$obBscLoteFinal->setValue                           ( $inCodLoteFinal                         );
$obBscLoteFinal->setSize                            ( 10                                      );
$obBscLoteFinal->setMaxLength                       ( 10                                      );
$obBscLoteFinal->setNull                            ( true                                    );
$obBscLoteFinal->setInteiro                         ( true                                    );

//INSCRIÇÃO IMOBILIARIA
$obBscInscricaoImobiliaria = new BuscaInnerIntervalo;
$obBscInscricaoImobiliaria->setRotulo               ( "Inscrição Imobiliária"                 );
$obBscInscricaoImobiliaria->obLabelIntervalo->setValue ( "até"                                );
$obBscInscricaoImobiliaria->obCampoCod->setName     ("inNumInscricaoImobiliariaInicial"       );
$obBscInscricaoImobiliaria->obCampoCod->setValue    ( $inNumInscricaoImobiliariaInicial       );
$obBscInscricaoImobiliaria->setFuncaoBusca          ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaInicial','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));
$obBscInscricaoImobiliaria->obCampoCod2->setName    ( "inNumInscricaoImobiliariaFinal"        );
$obBscInscricaoImobiliaria->obCampoCod2->setValue   ( $inNumInscricaoImobiliariaFinal         );
$obBscInscricaoImobiliaria->setFuncaoBusca2         ( str_replace("'","&quot;","abrePopUp('".CAM_GT_CIM_POPUPS."imovel/FLProcurarImovel.php','frm','inNumInscricaoImobiliariaFinal','stNumeroDomicilio','todos','".Sessao::getId()."','800','550');"));

//ORDENÇÃO
$obCmbOrder = new Select;
$obCmbOrder->setName                                ( "stOrder"                               );
$obCmbOrder->setRotulo                              ( "Ordenação"                             );
$obCmbOrder->setTitle                               ( "Escolha a ordenação do relatório"      );
$obCmbOrder->addOption                              ( "", "Selecione"                         );
$obCmbOrder->addOption                              ( "imovel_lote.inscricao_municipal" , "Inscrição"   );
$obCmbOrder->addOption                              ( "localizacao.nom_localizacao"     , "Localização" );
$obCmbOrder->addOption                              ( "lote_localizacao.valor"          , "Lote"        );
$obCmbOrder->setCampoDesc                           ( "stOrder"                               );
$obCmbOrder->setNull                                ( false                                   );
$obCmbOrder->setStyle                               ( "width: 200px"                          );

//DEFINIÇÃO DO FORMULÁRIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnAcao    );
$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->agrupaComponentes( array( $obCodInicioLocalizacao,   $obLblPeriodoLocalizacao , $obCodTerminoLocalizacao  ) );
$obFormulario->agrupaComponentes( array( $obBscLoteInicio,          $obLblPeriodoLote,         $obBscLoteFinal           ) );
$obFormulario->addComponente ( $obBscInscricaoImobiliaria );
$obFormulario->addComponente( $obCmbOrder );
$obFormulario->OK();
$obFormulario->setFormFocus( $obCodInicioLocalizacao->getid() );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
