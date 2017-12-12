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
* Arquivo de instância para manutenção de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 23167 $
$Name$
$Author: leandro.zis $
$Date: 2007-06-11 17:02:52 -0300 (Seg, 11 Jun 2007) $

Casos de uso: uc-01.04.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GA_NORMAS_NEGOCIO."RNorma.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//MANTEM O FILTRO E A PAGINACAO
$stLink = "&pg=".Sessao::read('linkPopUp_pg')."&pos=".Sessao::read('linkPopUp_pos')."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterNorma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LSNorma.php?".Sessao::getId()."&stAcao=$stAcao&inCodNorma=".$_POST['inCodNorma']."&inCodTipoNorma=".$_POST['inCodTipoNorma'].$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao&inCodNorma=".$_POST['inCodNorma']."&inCodTipoNorma=".$_POST['inCodTipoNorma'];
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao&inCodNorma=".$_POST['inCodNorma']."&inCodTipoNorma=".$_POST['inCodTipoNorma'];
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RNorma;

$obAtributos = new MontaAtributos;
$obAtributos->setName('Atributo_');
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {
    case "incluir":
        foreach ($arChave as $key=>$value) {
            $arChaves = preg_split( "/[^a-zA-Z0-9]/", $key );
            $inCodAtributo = $arChaves[0];
            if( is_array($value) )
                $value = implode(",",$value);
            $obRegra->obRTipoNorma->obRCadastroDinamico->addAtributosDinamicos( $inCodAtributo , $value );
        }

        $obRegra->setNumNorma                  ( $_POST['inNumNorma']        );
        $obRegra->setExercicio                 ( $_POST['stExercicio']       );
        $obRegra->setDataPublicacao            ( $_POST['stDataPublicacao']  );
        $obRegra->setDataAssinatura            ( $_POST['stDataAssinatura']  );
        $obRegra->setDataTermino               ( $_POST['stDataTermino']     );
        $obRegra->setNomeNorma                 ( $_POST['stNomeNorma']       );
        $obRegra->setDescricaoNorma            ( $_POST['stDescricao']       );
        $obRegra->setUrl                       ( Sessao::read('stNormaLink') );
        $obRegra->obRTipoNorma->setCodTipoNorma( $_POST['inCodTipoNorma']    );

        $obTransacao = new Transacao;
        $obErro = $obRegra->salvar($obTransacao);
        
        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAvisoPopUp($pgList,"Norma: ".$_POST['inNumNorma'],"incluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;
}

?>
