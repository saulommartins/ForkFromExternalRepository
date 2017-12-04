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
    * Página de processamento oculto para o relatório de bairros
    * Data de Criação   : 23/03/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Marcelo Boezio Paulino

    * @ignore

    * $Id: OCBairros.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.01.19
*/

/*
$Log$
Revision 1.8  2006/09/18 10:31:34  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMBairro.class.php"           );
include_once( CAM_FW_PDF."RRelatorio.class.php"                   );
include_once( CAM_GT_CIM_NEGOCIO."RCIMRelatorioBairros.class.php" );
include_once( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma          = "Bairros";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgFormNivel         = "FM".$stPrograma."Nivel.php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";
$pgJs                = "JS".$stPrograma.".js";
include_once( $pgJs );

// INSTANCIA OBJETO
$obRCIMBairro           = new RCIMBairro;
$obRRelatorio           = new RRelatorio;
$obRCIMRelatorioBairros = new RCIMRelatorioBairros;

$obRCIMConfiguracao     = new RCIMConfiguracao;
$obRCIMConfiguracao->consultarConfiguracao();

$boRSMD = false;
$rsRSMD = $obRCIMConfiguracao->getRSMD();
$rsRSMD->setPrimeiroElemento();

    while ( !$rsRSMD->eof() ) {
      if ( $rsRSMD->getCampo("nome") == "Bairro" ) {
          $boRSMD = true;
      }
      $rsRSMD->proximo();
    }
Sessao::write('boRSMD', $boRSMD);

$boAliquota = false;
$rsAliquota = $obRCIMConfiguracao->getRSAliquota();
$rsAliquota->setPrimeiroElemento();

    while ( !$rsAliquota->eof() ) {
        if ( $rsAliquota->getCampo("nome") == "Bairro" ) {
            $boAliquota = true;
        }
        $rsAliquota->proximo();
    }
Sessao::write('boAliquota', $boAliquota);

$arFiltro = Sessao::read('filtroRelatorio');
// SETA ATRIBUTOS DA REGRA QUE IRA GERAR O FILTRO DO RELATORIO
$obRCIMRelatorioBairros->obRBairro->setNomeBairro     ( $arFiltro['stNomBairro'   ]    );
$obRCIMRelatorioBairros->setCodInicio                 ( $arFiltro['inCodInicio'   ]    );
$obRCIMRelatorioBairros->setCodTermino                ( $arFiltro['inCodTermino'  ]    );
$obRCIMRelatorioBairros->obRBairro->setCodigoMunicipio( $arFiltro['inCodigoMunicipio'] );
$obRCIMRelatorioBairros->obRBairro->setCodigoUF       ( $arFiltro['inCodigoUF'       ] );
$obRCIMRelatorioBairros->setOrder                     ( $arFiltro['stOrder']           );
$obRCIMRelatorioBairros->setboRSMD     ( $boRSMD );
$obRCIMRelatorioBairros->setboAliquota ( $boAliquota );

// GERA RELATORIO ATRAVES DO FILTRO SETADO
$obRCIMRelatorioBairros->getRecordSetValor( $rsBairros );

Sessao::write('sessao_transf5', $rsBairros);
$obRRelatorio->executaFrameOculto( "OCGeraRelatorioBairros.php" );

// SELECIONA ACAO

switch ($_REQUEST ["stCtrl"]) {
    case "preencheMunicipio":
        Sessao::remove('municipio' );
        $js = "f.inCodigoMunicipio.value=''; \n";
        $js .= "limpaSelect(f.inCodMunicipio,0); \n";
        $js .= "f.inCodMunicipio[0] = new Option('Selecione','', 'selected');\n";
        $rsMunicipios = new RecordSet;

        if ($_REQUEST["inCodigoUF"]) {
            $obRCIMBairro->setCodigoUF( $_REQUEST["inCodUF"] );
            $obRCIMBairro->listarMunicipios( $rsMunicipios );
            $rsMunicipios->SetPrimeiroElemento();
            $inContador = 1;
            while ( !$rsMunicipios->eof() ) {
                $inCodMunicipio = $rsMunicipios->getCampo( "cod_municipio" );
                $stNomMunicipio = $rsMunicipios->getCampo( "nom_municipio" );
                $js .= "f.inCodMunicipio.options[$inContador] = new
Option(\"".$stNomMunicipio."\",\"".$inCodMunicipio."\"); \n";
                $inContador++;

                //carrega municipos na sessao para a exibição de filtro no rodapé do relatorio
                $arFiltro['municipio'][$rsMunicipios->getCampo( 'cod_municipio' )] = $rsMunicipios->getCampo( 'nom_municipio' );

                $rsMunicipios->proximo();
            }
        }

        Sessao::write('filtroRelatorio', $arFiltro);
        if ($_REQUEST["stLimpar"] == "limpar") {
            $js .= "f.inCodigoMunicipio.value='".$_REQUEST["inCodigoMunicipio"]."'; \n";
            $js .= "f.inCodMunicipio.options[".$_REQUEST["inCodigoMunicipio"]."].selected = true; \n";
        }
        SistemaLegado::executaFrameOculto($js);
    break;

}
?>
