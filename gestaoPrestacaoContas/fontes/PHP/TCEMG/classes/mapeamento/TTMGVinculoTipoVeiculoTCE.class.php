<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Solues em Gesto Pblica                                *
    * @copyright (c) 2013 Confederao Nacional de Municpos                         *
    * @author Confederao Nacional de Municpios                                    *
    *                                                                                *
    * Este programa  software livre; voc pode redistribu-lo e/ou modific-lo  sob *
    * os termos da Licena Pblica Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a verso 2 da Licena, como (a seu critrio) qualquer verso *
    *                                                                                *
    * Este  programa    distribudo  na  expectativa  de  que  seja  til,   porm, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implcita  de  COMERCIABILIDADE  OU *
    * ADEQUAÌO A UMA FINALIDADE ESPECêFICA. Consulte a Licena Pblica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Voc deve ter recebido uma cpia da Licena Pblica Geral  do  GNU  junto  com *
    * este programa; se no, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endereo 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Titulo do arquivo : Arquivo de mapeamento da tabela tcmgo.tipo_veiculo_tcm
    * Data de Criao   : 22/12/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id: TTMGVinculoTipoVeiculoTCE.class.php 59719 2014-09-08 15:00:53Z franver $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTMGVinculoTipoVeiculoTCE extends Persistente
{
/**
    * Mtodo Construtor
    * @access Private
*/
    public function TTMGVinculoTipoVeiculoTCE()
    {
        parent::Persistente();
        $this->setTabela('tcemg.tipo_veiculo_vinculo');
        $this->setCampoCod('cod_tipo');
        $this->setComplementoChave('');
        $this->AddCampo('cod_tipo'       , 'integer', true, ''   , true , true);
        $this->AddCampo('cod_tipo_tce'   , 'integer', true, ''   , false, true);
        $this->AddCampo('cod_subtipo_tce', 'integer', true, ''   , false, true);
    }
    
    public function __destruct(){}

}
