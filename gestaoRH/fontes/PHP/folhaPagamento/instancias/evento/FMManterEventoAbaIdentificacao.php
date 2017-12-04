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
    * Página de Formulario de Evento - Aba Identificacao
    * Data de Criação   : 29/08/2005

    * @author Programador: Eduardo Antunez

    * Caso de uso: uc-04.05.06

    $Id: FMManterEventoAbaIdentificacao.php 59922 2014-09-22 14:52:40Z franver $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

if ($stAcao == 'incluir') {
    $obTxtCodEventoIde = new TextBox;
    $obTxtCodEventoIde->setRotulo                    ( "Código"                                           );
    $obTxtCodEventoIde->setTitle                     ( "Informe o código do evento"                       );
    $obTxtCodEventoIde->setName                      ( "stCodigo"                                         );
    $obTxtCodEventoIde->setValue                     ( $stCodigo                                          );
    $obTxtCodEventoIde->setSize                      ( 10                                                 );
    $obTxtCodEventoIde->setMaxLength                 ( 5                                                  );
    $obTxtCodEventoIde->setNull                      ( false                                              );
    //$obTxtCodEventoIde->setCaracteresAceitos         ( "[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ-]"         );
    //$obTxtCodEventoIde->setEspacosExtras             ( false                                              );
    $obTxtCodEventoIde->setMascara                   ( $stMascaraEvento                                   );
    $obTxtCodEventoIde->setPreencheComZeros          ( 'E'                                                );

    $obTxtDescricaoIde = new TextBox;
    $obTxtDescricaoIde->setRotulo                    ( "Descrição"                                        );
    $obTxtDescricaoIde->setTitle                     ( "Informe a descrição do evento "                   );
    $obTxtDescricaoIde->setName                      ( "stDescricaoIde"                                   );
    $obTxtDescricaoIde->setValue                     ( $stDescricaoIde                                    );
    $obTxtDescricaoIde->setSize                      ( 50                                                 );
    $obTxtDescricaoIde->setMaxLength                 ( 80                                                 );
    $obTxtDescricaoIde->setNull                      ( false                                              );
    $obTxtDescricaoIde->setCaracteresAceitos         ( "[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ%--+*/%]"   );
    $obTxtDescricaoIde->setEspacosExtras             ( false                                              );
    $obTxtDescricaoIde->obEvento->setOnChange        ( "replicaDescricao(this.value)"                     );

    $obRdbNaturezaInformativoIde = new Radio;
    $obRdbNaturezaInformativoIde->setRotulo          ( "Natureza"                                            );
    $obRdbNaturezaInformativoIde->setName            ( "natureza"                                            );
    $obRdbNaturezaInformativoIde->setLabel           ( "Informativo"                                         );
    $obRdbNaturezaInformativoIde->setValue           ( "I"                                                   );
    $obRdbNaturezaInformativoIde->setTitle           ( "Selecione o tipo do evento"                          );
    $obRdbNaturezaInformativoIde->obEvento->setOnClick( "buscaValor('gerarSpans'); ");
    $obRdbNaturezaInformativoIde->setNull            ( false                                                 );
    $boChecked = ( $stNatureza == "I" );
    $obRdbNaturezaInformativoIde->setChecked         ( $boChecked                                            );

    $obRdbNaturezaProventoIde = new Radio;
    $obRdbNaturezaProventoIde->setRotulo             ( "Natureza"                                         );
    $obRdbNaturezaProventoIde->setName               ( "natureza"                                         );
    $obRdbNaturezaProventoIde->setLabel              ( "Provento"                                         );
    $obRdbNaturezaProventoIde->setValue              ( "P"                                                );
    $obRdbNaturezaProventoIde->setTitle              ( "Selecione o tipo do evento"                       );
    $obRdbNaturezaProventoIde->obEvento->setOnClick  ( "buscaValor('gerarSpans'); ");
    $boChecked = ( $stNatureza == "P" || empty($stNatureza) );
    $obRdbNaturezaProventoIde->setChecked            ( $boChecked                                         );

    $obRdbNaturezaDescontoIde = new Radio;
    $obRdbNaturezaDescontoIde->setRotulo             ( "Natureza"                                         );
    $obRdbNaturezaDescontoIde->setName               ( "natureza"                                         );
    $obRdbNaturezaDescontoIde->setLabel              ( "Desconto"                                         );
    $obRdbNaturezaDescontoIde->setValue              ( "D"                                                );
    $obRdbNaturezaDescontoIde->setTitle              ( "Selecione o tipo do evento"                       );
    $obRdbNaturezaDescontoIde->obEvento->setOnClick  ( "buscaValor('gerarSpans'); ");
    $boChecked = ( $stNatureza == "D" );
    $obRdbNaturezaDescontoIde->setChecked            ( $boChecked                                         );

    $obRdbNaturezaBaseIde = new Radio;
    $obRdbNaturezaBaseIde->setRotulo                 ( "Natureza"                                         );
    $obRdbNaturezaBaseIde->setName                   ( "natureza"                                         );
    $obRdbNaturezaBaseIde->setLabel                  ( "Base"                                             );
    $obRdbNaturezaBaseIde->setValue                  ( "B"                                                );
    $obRdbNaturezaBaseIde->setTitle                  ( "Selecione o tipo do evento"                       );
    $obRdbNaturezaBaseIde->obEvento->setOnClick      ( "buscaValor('gerarSpans');");
    $boChecked = ( $stNatureza == "B" );
    $obRdbNaturezaBaseIde->setChecked                ( $boChecked                                         );

    $obHdnNatureza = new Hidden;
    $obHdnNatureza->setName( "hdnNatureza" );
    $obHdnNatureza->setValue( $natureza );

    $obRdbTipoFixoIde = new Radio;
    $obRdbTipoFixoIde->setRotulo          ( "Tipo"                                         );
    $obRdbTipoFixoIde->setName            ( "stTipo"                                       );
    $obRdbTipoFixoIde->setLabel           ( "Fixo"                                         );
    $obRdbTipoFixoIde->setValue           ( "F"                                            );
    $obRdbTipoFixoIde->setTitle           ( "Informe se o evento será tratado como evento fixo ou variável." );
    $obRdbTipoFixoIde->setNull            ( false                                          );
    $obRdbTipoFixoIde->setChecked         ( true                                           );
    $obRdbTipoFixoIde->obEvento->setOnClick( "buscaValor('montaTipoVariavel');"            );

    $obRdbTipoVariavelIde = new Radio;
    $obRdbTipoVariavelIde->setName        ( "stTipo"                                       );
    $obRdbTipoVariavelIde->setLabel       ( "Variável"                                     );
    $obRdbTipoVariavelIde->setValue       ( "V"                                            );
    $obRdbTipoVariavelIde->setNull        ( false                                          );
    $obRdbTipoVariavelIde->setChecked     ( false                                          );
    $obRdbTipoVariavelIde->obEvento->setOnClick( "buscaValor('montaTipoVariavel');"        );

    $obRdbFixarValorIde = new Radio;
    $obRdbFixarValorIde->setRotulo          ( "Fixar Evento"                                 );
    $obRdbFixarValorIde->setName            ( "stFixar"                                      );
    $obRdbFixarValorIde->setLabel           ( "Valor"                                        );
    $obRdbFixarValorIde->setValue           ( "V"                                            );
    $obRdbFixarValorIde->setTitle           ( "Informe se o evento será fixado por valor ou por quantidade." );
    $obRdbFixarValorIde->setNull            ( false                                          );
    $obRdbFixarValorIde->setChecked         ( true                                           );

    $obRdbFixarQuantidadeIde = new Radio;
    $obRdbFixarQuantidadeIde->setName        ( "stFixar"                                      );
    $obRdbFixarQuantidadeIde->setLabel       ( "Quantidade"                                   );
    $obRdbFixarQuantidadeIde->setValue       ( "Q"                                            );
    $obRdbFixarQuantidadeIde->setNull        ( false                                          );
    $obRdbFixarQuantidadeIde->setChecked     ( false                                          );

} else {

    $obTxtCodEventoIde = new Label;
    $obTxtCodEventoIde->setRotulo                    ( "Código"                                           );
    $obTxtCodEventoIde->setTitle                     ( "Código do Evento"                                 );
    $obTxtCodEventoIde->setValue                     ( $stCodigo                                          );

    $obHdnCodigoEvento = new Hidden;
    $obHdnCodigoEvento->setName  ( "stCodigo"  );
    $obHdnCodigoEvento->setValue ( $stCodigo );
    
    $obHdnCodVerbaRescisoriaMTE = new Hidden;
    $obHdnCodVerbaRescisoriaMTE->setName  ( "stHdnCodVerbaRescisoriaMTE" );
    $obHdnCodVerbaRescisoriaMTE->setValue ( $stCodVerbaRescisoriaMTE  );

    $obTxtDescricaoIde = new Label;
    $obTxtDescricaoIde->setRotulo                    ( "Descrição"                                        );
    $obTxtDescricaoIde->setTitle                     ( "Descrição do Evento"                              );
    $obTxtDescricaoIde->setValue                     ( $stDescricaoIde                                    );

    switch ($stNatureza) {
        case "I": $stLblNatureza = "Informativo"; break;
        case "P": $stLblNatureza = "Provento";    break;
        case "D": $stLblNatureza = "Desconto";    break;
        case "B": $stLblNatureza = "Base";        break;
    }

    $obLblNaturezaIde = new Label;
    $obLblNaturezaIde->setRotulo                    ( "Natureza"                                          );
    $obLblNaturezaIde->setTitle                     ( "Tipo do Evento"                                    );
    $obLblNaturezaIde->setValue                     ( $stLblNatureza                                      );

    $obHdnNatureza = new Hidden;
    $obHdnNatureza->setName( "hdnNatureza" );
    $obHdnNatureza->setValue( $stLblNatureza );

    $obLblTipoIde = new Label;
    $obLblTipoIde->setRotulo                        ( "Tipo"                                              );
    $obLblTipoIde->setValue                         ( ($stTipo == 'V') ? 'Variável' : 'Fixo'              );

    $obLblFixarIde = new Label;
    $obLblFixarIde->setRotulo                       ( "Fixar Evento"                                      );
    $obLblFixarIde->setValue                        ( ($stFixado == 'Q') ? 'Quantidade' : 'Valor'         );

    $obLblLimiteIde = new Label;
    $obLblLimiteIde->setRotulo                      ( "Mês/Ano Limite Para Cálculo"                       );
    $obLblLimiteIde->setValue                       ( ($boLimiteCalculo == 'S') ? 'Sim' : 'Não'           );

    $obLblParcelaIde = new Label;
    $obLblParcelaIde->setRotulo                     ( "Apresentar Parcela"                                );
    $obLblParcelaIde->setValue                      ( ($boApresentaParcela == 'S') ? 'Sim' : 'Não'        );

    $obTxtValorIde = new Numerico;
    $obTxtValorIde->setName                          ( "nuValor"                                          );
    $obTxtValorIde->setAlign                         ( "RIGHT"                                            );
    $obTxtValorIde->setRotulo                        ( "Valor/Quantidade Padrão"                          );
    $obTxtValorIde->setMaxLength                     ( 14                                                 );
    $obTxtValorIde->setMaxValue                      ( 999999999.99                                       );
    $obTxtValorIde->setSize                          ( 12                                                 );
    $obTxtValorIde->setDecimais                      ( 2                                                  );
    $obTxtValorIde->setNegativo                      ( false                                              );
    $obTxtValorIde->setNull                          ( true                                               );
    $obTxtValorIde->setValue                         ( $nuValor                                           );
    $obTxtValorIde->setTitle                         ( "Informe o valor do evento"                        );

    $obTxtUnidadeQuantitativaIde = new Numerico;
    $obTxtUnidadeQuantitativaIde->setName            ( "nuUnidadeQuantitativa"                            );
    $obTxtUnidadeQuantitativaIde->setAlign           ( "RIGHT"                                            );
    $obTxtUnidadeQuantitativaIde->setRotulo          ( "Unidade Quantitativa"                             );
    $obTxtUnidadeQuantitativaIde->setMaxLength       ( 14                                                 );
    $obTxtUnidadeQuantitativaIde->setMaxValue        ( 999999999.99                                       );
    $obTxtUnidadeQuantitativaIde->setSize            ( 12                                                 );
    $obTxtUnidadeQuantitativaIde->setDecimais        ( 2                                                  );
    $obTxtUnidadeQuantitativaIde->setNegativo        ( false                                              );
    $obTxtUnidadeQuantitativaIde->setNull            ( true                                               );
    $obTxtUnidadeQuantitativaIde->setValue           ( $nuUnidadeQuantitativa                             );
    $obTxtUnidadeQuantitativaIde->setTitle           ( "Informe a quantitativa ou valor que representa a integralidade para uma competência" );

}

$obSpnContraChequeNatureza = new Span();
$obSpnContraChequeNatureza->setId('spnContraChequeNatureza');

$obTxtSigla = new TextBox;
$obTxtSigla->setRotulo                    ( "Sigla"                                        );
$obTxtSigla->setTitle                     ( "Digite uma sigla ou descrição reduzida para identificação do evento em alguns relatórios."                   );
$obTxtSigla->setName                      ( "stSigla"                                      );
$obTxtSigla->setValue                     ( $stSigla                                       );
$obTxtSigla->setSize                      ( 5                                              );
$obTxtSigla->setMaxLength                 ( 5                                              );

$obSpnBase = new Span;
$obSpnBase->setId('spnBase');

$obSpnTipoVariavel = new Span;
$obSpnTipoVariavel->setId ( "spnTipoVariavel" );

$obTxtTextoComplementarIde = new TextArea;
$obTxtTextoComplementarIde->setName              ( "stTextoComplementar"                              );
$obTxtTextoComplementarIde->setRotulo            ( "Texto Complementar"                               );
$obTxtTextoComplementarIde->setValue             ( $stTextoComplementar                               );
$obTxtTextoComplementarIde->setNull              ( true                                               );
$obTxtTextoComplementarIde->setTitle             ( "Informe um texto complementar relacionado à existência do evento" );
$obTxtTextoComplementarIde->setMaxCaracteres     ( 250                                                );

if ($stAcao != 'incluir') {

    $obLblEventoAutomatico = new Label;
    $obLblEventoAutomatico->setRotulo ("Evento Automático do Sistema"        );
    $obLblEventoAutomatico->setTitle  ( "Informe se o evento será utilizado pelo sistema em alguma configuração de evento automático. " );

    $obLblEventoAutomatico->setName ( 'lblEventoAutomatico' );
    if ($stEventoAutomatico) {
        $obLblEventoAutomatico->setValue ( 'Sim' );
    } else {
        $obLblEventoAutomatico->setValue ( 'Não' );
    }

} else {
    $obRdbEventoAutomaticoSim   = new Radio;
    $obRdbEventoAutomaticoSim->setRotulo          ( "Evento Automático do Sistema"        );
    $obRdbEventoAutomaticoSim->setName            ( "stEventoAutomatico"                  );
    $obRdbEventoAutomaticoSim->setLabel           ( "Sim"                                 );
    $obRdbEventoAutomaticoSim->setValue           ( "S"                                   );
    $obRdbEventoAutomaticoSim->setTitle           ( "Informe se o evento será utilizado pelo sistema em alguma configuração de evento automático. " );
    $obRdbEventoAutomaticoSim->setNull            ( false                                 );
    $obRdbEventoAutomaticoSim->setChecked         ( false                                  );

    $obRdbEventoAutomaticoNao  = new Radio;
    $obRdbEventoAutomaticoNao->setName        ( "stEventoAutomatico"                      );
    $obRdbEventoAutomaticoNao->setLabel       ( "Não"                                     );
    $obRdbEventoAutomaticoNao->setValue       ( "N"                                       );
    $obRdbEventoAutomaticoNao->setNull        ( false                                     );
    $obRdbEventoAutomaticoNao->setChecked     ( True                                      );
}

$obSpnVerbaRescisoriaMTE = new Span();
$obSpnVerbaRescisoriaMTE->setId('spnVerbaRescisoriaMTE');

$obCmbSequenciaNumeroIde = new Select;
$obCmbSequenciaNumeroIde->setRotulo       ( "Número"                                               );
$obCmbSequenciaNumeroIde->setName         ( "inCodSequencia"                                       );
$obCmbSequenciaNumeroIde->setStyle        ( "width: 200px"                                         );
$obCmbSequenciaNumeroIde->setTitle        ( "Informe a sequência que o evento deve ser calculado." );
$obCmbSequenciaNumeroIde->setCampoID      ( "cod_sequencia"                                        );
$obCmbSequenciaNumeroIde->setCampoDesc    ( "sequencia"                                            );
$obCmbSequenciaNumeroIde->addOption       ( "", "Selecione"                                        );
$obCmbSequenciaNumeroIde->setValue        ( $inCodSequencia                                        );
$obCmbSequenciaNumeroIde->setNull         ( false                                                  );
$obCmbSequenciaNumeroIde->preencheCombo   ( $rsSequencia                                           );
$obCmbSequenciaNumeroIde->obEvento->setOnChange("buscaValor('preencheSequencia');"                 );

$obLblSequenciaDescricaoIde = new Label;
$obLblSequenciaDescricaoIde->setRotulo( 'Descrição'            );
$obLblSequenciaDescricaoIde->setId    ( 'stSequenciaDescricao' );

$obLblSequenciaComplementoIde = new Label;
$obLblSequenciaComplementoIde->setRotulo( 'Complemento'            );
$obLblSequenciaComplementoIde->setId    ( 'stSequenciaComplemento' );

?>
