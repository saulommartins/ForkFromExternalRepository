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
    * Data de Criação: 23/10/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 23/10/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAPartConv extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio', Sessao::getExercicio() );
}

public function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaDadosTribunal()
{
    $stSql .= " SELECT 1 AS tipo_registro
                     , convenio.exercicio
                     , convenio.num_convenio
                     , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                     , CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN 1
                            ELSE 2
                     END AS tipo_pessoa
                     , CASE WHEN sw_cgm_pessoa_fisica.cpf IS NOT NULL THEN sw_cgm_pessoa_fisica.cpf
                            WHEN sw_cgm_pessoa_juridica.cnpj IS NOT NULL THEN sw_cgm_pessoa_juridica.cnpj
                            ELSE ''            
                     END AS cpf_cnpj            
                    , sem_acentos(sw_cgm.nom_cgm) as nom_cgm
                    , participante_convenio.valor_participacao
                    , '' AS reservado_tcm
                    , TO_CHAR(convenio.dt_assinatura, 'yyyymm') AS competencia
                    , participante_convenio.funcao AS nome_funcao
                    , TO_CHAR(convenio.dt_assinatura,'dd/mm/yyyy') AS data_assinatura
                    , TO_CHAR(convenio.dt_vigencia,'dd/mm/yyyy') AS data_vigencia

                 FROM licitacao.convenio

           INNER JOIN licitacao.participante_convenio
                   ON participante_convenio.exercicio = convenio.exercicio
                  AND participante_convenio.num_convenio = convenio.num_convenio
           
           INNER JOIN sw_cgm
                   ON sw_cgm.numcgm = participante_convenio.cgm_fornecedor

             LEFT JOIN sw_cgm_pessoa_fisica
                    ON sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

             LEFT JOIN sw_cgm_pessoa_juridica
                    ON sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                 WHERE convenio.exercicio = '".$this->getDado('exercicio')."'
                   AND convenio.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
    ";

    return $stSql;
}

}
