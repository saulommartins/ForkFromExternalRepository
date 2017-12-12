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
    * Data de Criação: 08/11/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    $Id: TFrotaVeiculoBaixado.class.php 65815 2016-06-20 18:28:29Z arthur $

    * Casos de uso: uc-03.02.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TFrotaVeiculoBaixado extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function TFrotaVeiculoBaixado()
    {
        parent::Persistente();
        $this->setTabela('frota.veiculo_baixado');
        $this->setCampoCod('cod_veiculo');
    
        $this->AddCampo('cod_veiculo'    ,'integer',true,'',true ,true );
        $this->AddCampo('dt_baixa'       ,'date'   ,true,'',false,false);
        $this->AddCampo('motivo'         ,'text'   ,true,'',false,false);
        $this->AddCampo('cod_tipo_baixa' ,'integer',true,'',false,true);
    }
    
    public function recuperaTipoBaixa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaTipoBaixa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    public function montaRecuperaTipoBaixa()
    {
        $stSql = " SELECT * from frota.tipo_baixa ";
        return $stSql;
    }
    
    public function recuperaBaixaVeiculoProprio(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaBaixaVeiculoProprio().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaBaixaVeiculoProprio()
    {
        $stSql = " SELECT proprios.cod_bem
                        , proprios.cod_veiculo
                        , CASE WHEN terceiros.timestamp IS NOT NULL AND terceiros.timestamp > proprios.timestamp THEN
                            terceiros.proprio
                         ELSE
                            proprios.proprio
                         END AS veiculo_proprio
                          
                     FROM (
                            SELECT MAX(proprio.timestamp) AS timestamp
                                 , proprio.cod_veiculo
                                 , proprio.cod_bem
                                 , veiculo_propriedade.proprio
                    
                              FROM frota.veiculo_propriedade
                    
                        INNER JOIN frota.proprio
                                ON veiculo_propriedade.cod_veiculo = proprio.cod_veiculo
                               AND veiculo_propriedade.timestamp   = proprio.timestamp
                    
                             WHERE proprio.cod_bem IN (".$this->getDado('stCodBem').")
                          GROUP BY proprio.cod_veiculo
                                 , veiculo_propriedade.proprio
                                 , cod_bem
                          ) AS proprios
               
                LEFT JOIN (
                            SELECT MAX (terceiros.timestamp) AS timestamp
                                 , terceiros.cod_veiculo
                                 , veiculo_propriedade.proprio
                                 
                              FROM frota.veiculo_propriedade
                    
                        INNER JOIN frota.terceiros
                                ON veiculo_propriedade.cod_veiculo = terceiros.cod_veiculo
                               AND veiculo_propriedade.timestamp   = terceiros.timestamp
                    
                          GROUP BY terceiros.cod_veiculo
                                 , veiculo_propriedade.proprio
                         ) AS terceiros
                       ON terceiros.cod_veiculo = proprios.cod_veiculo
               
               INNER JOIN frota.veiculo_propriedade
                       ON veiculo_propriedade.cod_veiculo = proprios.cod_veiculo
                       
                    WHERE NOT EXISTS ( SELECT 1 
                                         FROM frota.veiculo_baixado
                                        WHERE veiculo_baixado.cod_veiculo = proprios.cod_veiculo
                                     )

                 GROUP BY proprios.cod_bem
                        , veiculo_proprio
                        , proprios.cod_veiculo
               
                 ORDER BY proprios.cod_bem \n";
        return $stSql;
    }
    
    public function recuperaUltimaBaixa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaUltimaBaixa().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaRecuperaUltimaBaixa()
    {
            $stSql = "  SELECT proprio.cod_veiculo
                             , MAX(proprio.timestamp) AS timestamp
                       
                          FROM frota.veiculo_baixado
          
                    INNER JOIN frota.veiculo
                            ON veiculo.cod_veiculo = veiculo_baixado.cod_veiculo
          
                    INNER JOIN frota.veiculo_propriedade
                            ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
        
                    INNER JOIN frota.proprio
                            ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                           AND proprio.timestamp   = veiculo_propriedade.timestamp
        
                    INNER JOIN patrimonio.bem
                            ON bem.cod_bem = proprio.cod_bem
        
                         WHERE bem.cod_bem = ".$this->getDado('stCodBem')."
                      GROUP BY proprio.cod_veiculo ";

        return $stSql;
    }

}

?>