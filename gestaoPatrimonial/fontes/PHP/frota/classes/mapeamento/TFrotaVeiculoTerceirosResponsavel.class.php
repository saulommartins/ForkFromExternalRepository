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
    * Data de Criação: 12/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    * $Id: TFrotaVeiculoTerceirosResponsavel.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.10

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaVeiculoTerceirosResponsavel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFrotaVeiculoTerceirosResponsavel()
{
    parent::Persistente();
    $this->setTabela('frota.veiculo_terceiros_responsavel');
    $this->setCampoCod('cod_veiculo');
    $this->setComplementoChave( 'timestamp' );

    $this->AddCampo('cod_veiculo','integer',true,'',true,true);
    $this->AddCampo('timestamp'  ,'timestamp',false,'',false,false);
    $this->AddCampo('numcgm','integer',true,'',false,true);
    $this->AddCampo('dt_inicio','date',true,'',false,false);
    $this->AddCampo('dt_fim','date',true,'',false,false);

}

    public function recuperaUltimoResponsavel(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaUltimoResponsavel",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaUltimoResponsavel()
    {
        $stSql = "
            SELECT veiculo_terceiros_responsavel.cod_veiculo
                 , veiculo_terceiros_responsavel.numcgm
                 , TO_CHAR(veiculo_terceiros_responsavel.dt_inicio,'dd/mm/yyyy') as dt_inicio
                 , veiculo_terceiros_responsavel.timestamp
              FROM frota.veiculo_terceiros_responsavel
             WHERE ";
        if ( $this->getDado( 'cod_veiculo' ) ) {
            $stSql.= " veiculo_terceiros_responsavel.cod_veiculo = ".$this->getDado( 'cod_veiculo' )."  AND ";
        }
        if ( $this->getDado( 'dt_inicio' ) ) {
            $stSql.= " veiculo_terceiros_responsavel.dt_inicio = TO_DATE('".$this->getDado( 'dt_inicio' )."','dd/mm/yyyy')  AND ";
        }
        if ( $this->getDado( 'numcgm' ) ) {
            $stSql.= " veiculo_terceiros_responsavel.numcgm = ".$this->getDado( 'numcgm' )."  AND ";
        }

        $stSql = substr($stSql,0,-4);

        $stSql.= "
          ORDER BY timestamp DESC
             LIMIT 1
        ";

        return $stSql;
    }

}
