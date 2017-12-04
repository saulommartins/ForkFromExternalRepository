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
  * Página Mapeamento arquivo TCM-BA ObraMedicao
  * Data de Criação: 30/09/2015
  * @author Analista:      Valtair Santos 
  * @author Desenvolvedor: Jean da Silva
  * $Id: $
  * $Date: $
  * $Author: $
  * $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBAObraMedicao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }    

    public function recuperaTribunal (&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaTribunal()
    {
        $stSql = "SELECT '1' as tipo_registro
                         , '".$this->getDado('unidade_gestora')."' as  unidade_gestora
                         , obra.nro_obra AS num_obra
                         , obra_medicao.cod_medicao AS num_medicao
                         , obra_medicao.data_inicio AS dt_inicio_medicao
                         , obra_medicao.data_final AS dt_final_medicao
                         , obra_medicao.data_medicao
                         , obra_medicao.vl_medicao
                         , obra_medicao.nro_nota_fiscal AS num_nota_fiscal
                         , obra_medicao.data_nota_fiscal AS dt_nota_fiscal
                         , atestador.cpf AS cpf_atestador_1
                         , '' AS cpf_atestador_2
                         , responsavel_fiscal.codigo AS cpf_cnpj_resp_1
                         , obra_fiscal.registro_profissional AS registro_classe_1
                         , obra_fiscal.matricula AS matricula_fiscal_1
                         , '' AS cpf_cnpj_resp_2
                         , '' AS matricula_fiscal_2
                         , '' AS registro_classe_2
                         , '".$this->getDado('competencia')."' AS competencia

                    FROM tcmba.obra

              INNER JOIN tcmba.obra_fiscal
                      ON obra_fiscal.cod_obra = obra.cod_obra
                     AND obra_fiscal.cod_entidade = obra.cod_entidade
                     AND obra_fiscal.exercicio = obra.exercicio
                     AND obra_fiscal.cod_tipo = obra.cod_tipo
                     AND obra_fiscal.data_inicio = (SELECT MAX(OF.data_inicio) FROM tcmba.obra_fiscal AS OF
                                                     WHERE OF.cod_obra = obra_fiscal.cod_obra
                                                       AND OF.cod_entidade = obra_fiscal.cod_entidade
                                                       AND OF.exercicio = obra_fiscal.exercicio
                                                       AND OF.cod_tipo = obra_fiscal.cod_tipo
                                                   )

              INNER JOIN tcmba.obra_medicao
                      ON obra_medicao.cod_obra = obra.cod_obra
                     AND obra_medicao.cod_entidade = obra.cod_entidade
                     AND obra_medicao.exercicio = obra.exercicio
                     AND obra_medicao.cod_tipo = obra.cod_tipo

              INNER JOIN sw_cgm_pessoa_fisica AS atestador
                      ON atestador.numcgm = obra_medicao.numcgm

              INNER JOIN (
                          SELECT cpf AS codigo
                               , numcgm
                            FROM sw_cgm_pessoa_fisica
                           UNION
                          SELECT cnpj AS codigo
                               , numcgm
                            FROM sw_cgm_pessoa_juridica
                         ) AS responsavel_fiscal
                      ON responsavel_fiscal.numcgm = obra_fiscal.numcgm

                   WHERE obra_medicao.data_medicao BETWEEN TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy')
                                                       AND TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                ";
        return $stSql;
    }

}//fim da classe
?>
 