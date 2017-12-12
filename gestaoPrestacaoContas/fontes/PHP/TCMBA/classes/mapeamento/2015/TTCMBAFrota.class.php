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
    * Extensão da Classe de mapeamento
    * Data de Criação: 29/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 62823 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAFrota extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    parent::Persistente();
    
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
}

public function recuperaDados(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaDados()
{
    $stSql = "
            SELECT 1 AS tipo_registro
                 , ".$this->getDado('inCodGestora')." AS unidade_gestora
                 , veiculo.placa
                 , veiculo.cod_modelo AS tipo_veiculo
                 , veiculo.cod_marca AS marca
                 , CASE WHEN combustivel.cod_combustivel = 1 THEN 3
                        WHEN combustivel.cod_combustivel = 2 THEN 1
                        WHEN combustivel.cod_combustivel = 3 THEN 2
                   END AS tipo_combustivel
                 , veiculo.num_certificado AS num_renavam
                 , veiculo.chassi
                 , veiculo.ano_fabricacao
                 , veiculo_propriedade.proprio
                 , bem_comprado.nota_fiscal
                 , bem.vl_bem
                 , '1' AS anterior_siga
                 , bem_comprado.cod_empenho AS empenho
                 , TO_CHAR(veiculo.dt_aquisicao,'DDMMYYYY') AS dt_aquisicao 
                 , TO_CHAR(veiculo_baixado.dt_baixa,'DDMMYYYY') AS dt_baixa 

              FROM frota.veiculo

         LEFT JOIN ( SELECT veiculo_propriedade.cod_veiculo
                          , proprio.cod_bem
                          , CASE WHEN (proprio = true)
                                 THEN 'S'
                                 ELSE 'N'
                            END AS proprio
                       FROM frota.veiculo_propriedade
                 INNER JOIN ( SELECT cod_veiculo
                                   , MAX(timestamp) AS timestamp
                                FROM frota.veiculo_propriedade
                            GROUP BY cod_veiculo
                            ) AS veiculo_propriedade_max
                         ON veiculo_propriedade_max.cod_veiculo = veiculo_propriedade.cod_veiculo
                        AND veiculo_propriedade_max.timestamp   = veiculo_propriedade.timestamp
                  LEFT JOIN frota.proprio
                         ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                        AND proprio.timestamp   = veiculo_propriedade.timestamp
                   ) AS veiculo_propriedade
                ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo

         LEFT JOIN patrimonio.bem
                ON bem.cod_bem = veiculo_propriedade.cod_bem

         LEFT JOIN patrimonio.bem_comprado
                ON bem_comprado.cod_bem = bem.cod_bem

         LEFT JOIN frota.veiculo_baixado
                ON veiculo_baixado.cod_veiculo = veiculo.cod_veiculo

         LEFT JOIN frota.veiculo_locacao
                ON veiculo_locacao.cod_veiculo = veiculo.cod_veiculo

         LEFT JOIN frota.veiculo_combustivel
                ON veiculo_combustivel.cod_veiculo = veiculo.cod_veiculo

         LEFT JOIN frota.combustivel
                ON combustivel.cod_combustivel = veiculo_combustivel.cod_combustivel

             WHERE ( veiculo.dt_aquisicao BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                              AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                  OR veiculo_baixado.dt_baixa BETWEEN TO_DATE('".$this->getDado('dtInicio')."','dd/mm/yyyy')
                                                  AND TO_DATE('".$this->getDado('dtFim')."','dd/mm/yyyy')
                    )
                AND ( bem_comprado.cod_entidade = ".$this->getDado('stEntidade')." OR veiculo_locacao.cod_entidade = ".$this->getDado('stEntidade')." ) ";
    
    return $stSql;
}

}

?>