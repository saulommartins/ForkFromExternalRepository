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
    * Página de Processamento de Manter Assinaturas
    * Data de Criação: 08/05/2007

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Leandro André Zis

    * @ignore

    * Casos de uso: uc-01.01.08
*/

/*
$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterAssinaturas";
$pgForm     = "FM".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";

include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinatura.class.php"  );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinaturaCrc.class.php"  );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoAssinaturaModulo.class.php"  );

Sessao::setTrataExcecao( true );

$obTAdministracaoAssinatura = new TAdministracaoAssinatura;
$obTAdministracaoAssinaturaCrc = new TAdministracaoAssinaturaCrc;
$obTAdministracaoAssinaturaModulo = new TAdministracaoAssinaturaModulo;

$obTAdministracaoAssinaturaCrc->obTAdministracaoAssinatura = $obTAdministracaoAssinatura;
$obTAdministracaoAssinaturaModulo->obTAdministracaoAssinatura = $obTAdministracaoAssinatura;

$arSessaoAssinaturas = Sessao::read('assinaturas');

foreach ($arSessaoAssinaturas as $arAssinatura) {
    $obTAdministracaoAssinatura->setDado( 'exercicio'   , Sessao::getExercicio() );
    $obTAdministracaoAssinatura->setDado( 'cod_entidade', $arAssinatura['inCodEntidade'] );
    $obTAdministracaoAssinatura->setDado( 'numcgm'      , $arAssinatura['inCGM'] );
    $obTAdministracaoAssinatura->setDado( 'cargo'       , $arAssinatura['stCargo'] );
    $obTAdministracaoAssinatura->recuperaNow($stNow);
    $obTAdministracaoAssinatura->setDado( 'timestamp'    , $stNow );
    $obTAdministracaoAssinatura->inclusao();

    if ($arAssinatura['stCRC']) {
        $obTAdministracaoAssinaturaCrc->setDado( 'insc_crc', $arAssinatura['stCRC'] );
        $obTAdministracaoAssinaturaCrc->setDado( 'timestamp', $stNow );
        $obTAdministracaoAssinaturaCrc->inclusao();
    }
    foreach ($arAssinatura['arCodModulos'] as $inCodModulo) {
        $obTAdministracaoAssinaturaModulo->setDado( 'cod_modulo', $inCodModulo );
        $obTAdministracaoAssinaturaModulo->setDado( 'timestamp', $stNow );
        $obTAdministracaoAssinaturaModulo->inclusao();
    }
}

SistemaLegado::alertaAviso($pgForm, "Assinatura", "incluir", "aviso", Sessao::getId(), "../");
Sessao::encerraExcecao();

?>
