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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMGODividaFundada extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGODividaFundada()
    {
        parent::Persistente();

        $this->setTabela('tcmgo.divida_fundada');
        $this->setCampoCod('cod_norma');
        $this->setComplementoChave('exercicio, cod_entidade');
        
        //AddCampo($stNome, $stTipo, $boRequerido, $nrTamanho, $boPrimaryKey, $boForeignKey)
        $this->AddCampo('exercicio'            , 'character' , true  , '4'    , true  , false);
        $this->AddCampo('cod_entidade'         , 'integer'   , true  , ''     , true  , false);
        $this->AddCampo('num_unidade'          , 'integer'   , true  , ''     , false , false);
        $this->AddCampo('num_orgao'            , 'integer'   , true  , ''     , false , false);
        $this->AddCampo('cod_norma'            , 'integer'   , true  , ''     , true  , false);
        $this->AddCampo('numcgm'               , 'integer'   , false , ''     , false , false);
        $this->AddCampo('cod_tipo_lancamento'  , 'integer'   , true  , ''     , false , false);
        $this->AddCampo('valor_saldo_anterior' , 'numeric'   , true  , '14,2' , false , false);
        $this->AddCampo('valor_contratacao'    , 'numeric'   , true  , '14,2' , false , false);
        $this->AddCampo('valor_amortizacao'    , 'numeric'   , true  , '14,2' , false , false);
        $this->AddCampo('valor_cancelamento'   , 'numeric'   , true  , '14,2' , false , false);
        $this->AddCampo('valor_encampacao'     , 'numeric'   , true  , '14,2' , false , false);
        $this->AddCampo('valor_correcao'       , 'numeric'   , true  , '14,2' , false , false);
        $this->AddCampo('valor_saldo_atual'    , 'numeric'   , true  , '14,2' , false , false);
    }

    public function recuperaRegistro10(&$rsRecordSet,$stFiltro="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRegistro10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    public function montaRecuperaRegistro10()
    {
        $stSql = "  SELECT '10' AS tipo_registro
                            , divida_fundada.*
                            , TO_CHAR(norma.dt_publicacao,'ddmmyyyy') AS dt_lei
                            , sw_cgm.nom_cgm
                    FROM tcmgo.divida_fundada
                    JOIN normas.norma
                        ON norma.cod_norma = divida_fundada.cod_norma
                    JOIN sw_cgm
                        ON sw_cgm.numcgm = divida_fundada.numcgm
                ";

        return $stSql;
    }

}

?>

