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
    * Data de Criação: 07/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: TFrotaMotoristaVeiculo.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaMotoristaVeiculo extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */

    public function TFrotaMotoristaVeiculo()
    {
        parent::Persistente();
        $this->setTabela('frota.motorista_veiculo');
        $this->setCampoCod('');
        $this->setComplementoChave('cgm_motorista,cod_veiculo');
        $this->AddCampo('cod_veiculo'   ,'integer',true,'',true,true);
        $this->AddCampo('cgm_motorista' ,'integer',true,'',true,true);
        $this->AddCampo('padrao'        ,'boolean',false,'',false,false);
    }

    public function recuperaVeiculosMotorista(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaVeiculosMotorista",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaVeiculosMotorista()
    {
        $stSql = "
            SELECT motorista_veiculo.cgm_motorista
                 , motorista_veiculo.cod_veiculo
                 , marca.cod_marca
                 , marca.nom_marca
                 , modelo.cod_modelo
                 , modelo.nom_modelo
                 , motorista_veiculo.padrao
                 , CASE WHEN motorista_veiculo.padrao IS TRUE
                        THEN 'Sim'
                        ELSE 'Não'
                   END AS padrao_desc
              FROM frota.motorista_veiculo
        INNER JOIN frota.veiculo
                ON veiculo.cod_veiculo = motorista_veiculo.cod_veiculo
        INNER JOIN frota.marca
                ON marca.cod_marca = veiculo.cod_marca
        INNER JOIN frota.modelo
                ON modelo.cod_marca = veiculo.cod_marca
               AND modelo.cod_modelo = veiculo.cod_modelo
             WHERE ";
        if ( $this->getDado( 'cgm_motorista') ) {
            $stSql .= " motorista_veiculo.cgm_motorista = ".$this->getDado('cgm_motorista')." AND   ";
        }

        return substr( $stSql, 0, -6);
    }
}
