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
  * Página Mapeamento arquivo TCM-BA ContrataMdo
  * Data de Criação: 02/10/2015
  * @author Analista:      Valtair Santos 
  * @author Desenvolvedor: Jean da Silva
  * $Id: $
  * $Date: $
  * $Author: $
  * $Rev: $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTCMBATermoParc extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
    }    

    public function recuperaTribunal (&$rsRecordSet, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTribunal();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaTribunal()
    {
        $stSql = "SELECT '1' as tipo_registro
                         , '".$this->getDado('unidade_gestora')."' as  unidade_gestora
                         , termo_parceria.nro_processo
                         , termo_parceria.dt_assinatura
                         , termo_parceria.dt_publicacao
                         , termo_parceria.imprensa_oficial
                         , termo_parceria.objeto
                         , termo_parceria.dt_termino
                         , termo_parceria.dt_inicio
                         , termo_parceria.nro_processo_mj
                         , termo_parceria.dt_processo_mj
                         , termo_parceria.dt_publicacao_mj
                         , parceiro.cnpj AS cnpj_oscip
                         , parceiro.nom_cgm AS nome_pessoa
                         , termo_parceria.processo_licitatorio
                         , termo_parceria.processo_dispensa
                         , '".$this->getDado('competencia')."' AS competencia
                         , '' AS reservado_tcm
                         , termo_parceria.exercicio
                         , acao.num_acao AS proj_ativ
                         , SUBSTR(acao.num_acao::VARCHAR,1,1) AS tipo_proj_ativ
                         , despesa.num_orgao AS cod_orgao
                         , despesa.cod_funcao
                         , despesa.cod_subfuncao
                         , ppaprograma.num_programa AS cod_programa
                         , 2 AS indicador_siga

                    FROM tcmba.termo_parceria

              INNER JOIN tcmba.termo_parceria_dotacao
                      ON termo_parceria_dotacao.exercicio = termo_parceria.exercicio
                     AND termo_parceria_dotacao.cod_entidade = termo_parceria.cod_entidade
                     AND termo_parceria_dotacao.nro_processo = termo_parceria.nro_processo

              INNER JOIN orcamento.despesa
                      ON despesa.exercicio = termo_parceria_dotacao.exercicio
                     AND despesa.cod_despesa = termo_parceria_dotacao.cod_despesa

              INNER JOIN orcamento.pao
                      ON pao.exercicio = despesa.exercicio
                     AND pao.num_pao = despesa.num_pao

              INNER JOIN orcamento.pao_ppa_acao
                      ON pao_ppa_acao.exercicio = pao.exercicio
                     AND pao_ppa_acao.num_pao = pao.num_pao

              INNER JOIN ppa.acao
                      ON acao.cod_acao = pao_ppa_acao.cod_acao

              INNER JOIN orcamento.programa
                      ON programa.cod_programa = despesa.cod_programa
                     AND programa.exercicio = despesa.exercicio

              INNER JOIN orcamento.programa_ppa_programa
                      ON programa_ppa_programa.cod_programa = programa.cod_programa
                     AND programa_ppa_programa.exercicio = programa.exercicio

              INNER JOIN ppa.programa AS ppaprograma
                      ON ppaprograma.cod_programa = programa_ppa_programa.cod_programa_ppa
                      
              INNER JOIN (
                          SELECT sw_cgm_pessoa_juridica.cnpj
                               , sw_cgm.numcgm
                               , sw_cgm.nom_cgm
                            FROM sw_cgm
                      INNER JOIN sw_cgm_pessoa_juridica
                              ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm
                         ) AS parceiro
                      ON parceiro.numcgm = termo_parceria.numcgm

                   WHERE termo_parceria.dt_termino > TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy')
                     AND termo_parceria.dt_inicio < TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                     AND termo_parceria.cod_entidade IN (".$this->getDado('entidades').")
                     AND termo_parceria.exercicio  = '".$this->getDado('exercicio')."'
                ";
        return $stSql;
    }

}//fim da classe

?>
 