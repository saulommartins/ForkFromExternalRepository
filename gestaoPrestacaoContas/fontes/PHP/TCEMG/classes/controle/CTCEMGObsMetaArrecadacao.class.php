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
    * Classe de controle do arquivo obsMetaArrecadacao.txt
    * Data de Criação   : 21/01/2009

    * @author Analista      Tonismar Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    $Id:$
    */

class CTCEMGObsMetaArrecadacao
{

    public $obModel;

    public function __construct(&$obModel)
    {
        $this->obModel = $obModel;
    }

    public function incluir($arRequest)
    {
        $obErro = $this->obModel->incluirObsMetaArrecadacao($arRequest);

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FLObsMetaArrecadacao.php'.'?'.Sessao::getId().'&stAcao=incluir', '12/'.Sessao::getExercicio(), 'incluir','aviso', Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    public function alterar($arRequest)
    {
        $obErro = $this->obModel->alterarObsMetaArrecadacao($arRequest);

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FLObsMetaArrecadacao.php'.'?'.Sessao::getId().'&stAcao=alterar', $arRequest['inMes'].'/'.Sessao::getExercicio(), 'alterar','aviso', Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    public function carregaFrmAlteracao($arRequest)
    {
        $rsRecordSet = $this->obModel->consultaObsMetaArrecadacao($arRequest);

        if ($rsRecordSet->getNumLinhas() > 0) {
            $stJs  = "f.stObserv.value='".trim($rsRecordSet->getCampo('observacao'))."';";
            $stJs .= "f.stAcao.value='alterar';";
        } else {
            $stJs .= "f.stAcao.value='incluir';";
        }
        echo $stJs;
    }

    public function limpaCampos()
    {
        $stJs  = "f.stObserv.value='';";

        echo $stJs;
    }

}

?>
