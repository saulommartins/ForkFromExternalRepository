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

class TTCMBAContrataMdo extends Persistente
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
                         , contratado.cpf AS cpf_contratado
                         , contratado.nom_cgm AS nom_contratado
                         , obra_contratos.cod_contratacao AS tipo_contratacao
                         , obra_contratos.nro_instrumento AS num_instrumento
                         , obra_contratos.nro_contrato AS num_contrato
                         , obra_contratos.nro_convenio AS num_convenio
                         , obra_contratos.nro_parceria AS num_parceria
                         , obra_contratos.funcao_cgm AS funcao_contratada
                         , obra_contratos.data_inicio
                         , obra_contratos.data_final
                         , obra_contratos.lotacao
                         , '".$this->getDado('competencia')."' AS competencia
                         , '' AS reservado_tcm
                        , 2 AS indicador_siga

                    FROM tcmba.obra

              INNER JOIN tcmba.obra_contratos
                      ON obra_contratos.cod_obra = obra.cod_obra
                     AND obra_contratos.cod_entidade = obra.cod_entidade
                     AND obra_contratos.exercicio = obra.exercicio
                     AND obra_contratos.cod_tipo = obra.cod_tipo

              INNER JOIN (
                          SELECT sw_cgm_pessoa_fisica.cpf
                               , sw_cgm.numcgm
                               , sw_cgm.nom_cgm
                            FROM sw_cgm
                      INNER JOIN sw_cgm_pessoa_fisica
                              ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm
                         ) AS contratado
                      ON contratado.numcgm = obra_contratos.numcgm

                   WHERE obra_contratos.data_final > TO_DATE('".$this->getDado('data_inicial')."','dd/mm/yyyy')
                     AND obra_contratos.data_inicio < TO_DATE('".$this->getDado('data_final')."','dd/mm/yyyy')
                     AND obra_contratos.cod_entidade IN (".$this->getDado('entidades').")
                     AND obra_contratos.exercicio  = '".$this->getDado('exercicio')."'
                ";
        return $stSql;
    }

}//fim da classe

?>
 