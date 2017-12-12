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
    * Página de processamento oculto para o relatório de trechos
    * Data de Criação   : 31/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * Casos de uso: uc-05.01.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"               );
include_once( CAM_GT_CIM_NEGOCIO."RCIMRelatorioTrechos.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Trechos";
$pgFilt     = "FL".$stPrograma.".php";

// INSTANCIA OBJETO
$obRRelatorio           = new RRelatorio;
$obRCIMRelatorioTrechos = new RCIMRelatorioTrechos;
$obRCIMConfiguracao     = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();

$boRSMD = false;
$rsRSMD = $obRCIMConfiguracao->getRSMD();
$rsRSMD->setPrimeiroElemento();

while ( !$rsRSMD->eof() ) {
      if ( $rsRSMD->getCampo("nome") == "Trecho" ) {
          $boRSMD = true;
      }
      $rsRSMD->proximo();
    }
Sessao::write('boRSMD', $boRSMD);

$boAliquota = false;
$rsAliquota = $obRCIMConfiguracao->getRSAliquota();
$rsAliquota->setPrimeiroElemento();

    while ( !$rsAliquota->eof() ) {
        if ( $rsAliquota->getCampo("nome") == "Trecho" ) {
            $boAliquota = true;
        }
        $rsAliquota->proximo();
    }

Sessao::write('boAliquota', $boAliquota);
$arFiltro = Sessao::read('filtroRelatorio');
// SETA ATRIBUTOS DA REGRA QUE IRA GERAR O FILTRO DO RELATORIO
$obRCIMRelatorioTrechos->setCodInicio           ( $arFiltro['inCodInicio']                );
$obRCIMRelatorioTrechos->setCodTermino          ( $arFiltro['inCodTermino']               );
$obRCIMRelatorioTrechos->setCodInicioLogradouro ( $arFiltro['inCodInicioLogradouro']      );
$obRCIMRelatorioTrechos->setCodTerminoLogradouro( $arFiltro['inCodTerminoLogradouro']     );
$obRCIMRelatorioTrechos->setOrder               ( $arFiltro['stOrder']                    );
$obRCIMRelatorioTrechos->setTipoRelatorio       ( $arFiltro['stTipoRelatorio']            );
$obRCIMRelatorioTrechos->setAtributos           ( array_key_exists('inCodAtributosSelecionados', $arFiltro) ? $arFiltro['inCodAtributosSelecionados'] : '' );
$obRCIMRelatorioTrechos->setboRSMD              ( $boRSMD );
$obRCIMRelatorioTrechos->setboAliquota          ( $boAliquota );

// GERA RELATORIO ATRAVES DO FILTRO SETADO
$obRCIMRelatorioTrechos->getRecordSetValor( $rsTrechos , $arCabecalho );

Sessao::write('rsTrechos', $rsTrechos);
Sessao::write('arCabecalho', $arCabecalho);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioTrechos.php" );
?>
