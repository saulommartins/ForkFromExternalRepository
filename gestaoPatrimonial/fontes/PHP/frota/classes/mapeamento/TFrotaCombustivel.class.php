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
  * Mapeamento da tabela frota.veiculo_combustivel
  * Data de criação : 30/08/2007

  * @author Analista: Gelson W. Gonçalves
  * @author Programador: Henrique Boaventura

  * $Id: TFrotaCombustivel.class.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-03.02.06
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaCombustivel extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFrotaCombustivel()
    {
        parent::Persistente();
        $this->setTabela('frota.combustivel');
        $this->setCampoCod('cod_combustivel');

        $this->AddCampo('cod_combustivel','integer',true,'',true,false);
        $this->AddCampo('nom_combustivel','varchar',true,'15',false,false);
    }

    public function recuperaCombustivelVeiculo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaCombustivelVeiculo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaCombustivelVeiculo()
    {
        $stSql = "
            SELECT veiculo_combustivel.cod_veiculo
                 , veiculo_combustivel.cod_combustivel
                 , combustivel.nom_combustivel
              FROM frota.veiculo_combustivel
        INNER JOIN frota.combustivel
                ON combustivel.cod_combustivel = veiculo_combustivel.cod_combustivel
             WHERE
        ";
        if ( $this->getDado('cod_veiculo') ) {
            $stSql .= ' veiculo_combustivel.cod_veiculo = '.$this->getDado('cod_veiculo').' AND   ';
        }

        return substr($stSql,0,-6);
    }

    public function recuperaCombustivelDisponivelVeiculo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaCombustivelDisponivelVeiculo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaCombustivelDisponivelVeiculo()
    {
        $stSql = "
            SELECT combustivel.cod_combustivel
                 , combustivel.nom_combustivel
              FROM frota.combustivel
             WHERE NOT EXISTS( SELECT 1
                                 FROM frota.veiculo_combustivel
                                WHERE veiculo_combustivel.cod_veiculo = ".$this->getDado('cod_veiculo')."
                                  AND veiculo_combustivel.cod_combustivel = combustivel.cod_combustivel ) AND   ";

        return substr($stSql,0,-6);
    }

}
