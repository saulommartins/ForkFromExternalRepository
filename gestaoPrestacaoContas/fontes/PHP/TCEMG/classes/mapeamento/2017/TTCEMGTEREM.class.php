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
  * Arquivo de mapeamento - Exportação Arquivos TCEMG - TEREM.csv
  * Data de Criação: 14/03/2016

  * @author Analista:      Dagiane
  * @author Desenvolvedor: Jean
  *
  * @ignore
  * $Id: TTCEMGTEREM.class.php 65368 2016-05-16 20:26:35Z jean $
  * $Date: 2016-05-16 17:26:35 -0300 (Mon, 16 May 2016) $
  * $Author: jean $
  * $Rev: 65368 $
  *
*/
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TTCEMGTEREM extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDados(&$rsRecordSet, $stFiltro = "" , $stOrder = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if (trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false) ? " ORDER BY ".$stOrdem : $stOrdem;

        $stSql = $this->montaRecuperaDados().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDados()
    {
        $stSql = "
          SELECT 10 AS tipo_registro
               , teto_remuneratorio.teto AS vlparateto
               , CASE WHEN teto_remuneratorio_controle.cod_entidade IS NOT NULL
                      THEN 2
                      ELSE 1
                  END AS tipo_cadastro
               , teto_remuneratorio.justificativa AS just_alteracao
               , CASE WHEN teto_remuneratorio_controle.cod_entidade IS NULL
                        THEN TO_CHAR(teto_remuneratorio.vigencia,'ddmmyyyy')
                        ELSE ''
                 END AS dt_inicial
               , CASE WHEN teto_remuneratorio_controle.cod_entidade IS NOT NULL
                        THEN TO_CHAR((teto_remuneratorio.vigencia-1),'ddmmyyyy')
                        ELSE ''
                 END AS dt_final
               , sw_cgm_pessoa_juridica.cnpj
               
            FROM tcemg.teto_remuneratorio

       LEFT JOIN tcemg.teto_remuneratorio_controle
              ON teto_remuneratorio_controle.cod_entidade = teto_remuneratorio.cod_entidade
             AND teto_remuneratorio_controle.exercicio = teto_remuneratorio.exercicio

      INNER JOIN administracao.configuracao_entidade
              ON configuracao_entidade.exercicio = teto_remuneratorio.exercicio
             AND configuracao_entidade.cod_entidade = teto_remuneratorio.cod_entidade
             AND configuracao_entidade.parametro = 'tcemg_codigo_orgao_entidade_sicom'

      INNER JOIN orcamento.entidade
              ON entidade.cod_entidade = configuracao_entidade.cod_entidade
             AND entidade.exercicio = configuracao_entidade.exercicio

      INNER JOIN sw_cgm_pessoa_juridica
              ON sw_cgm_pessoa_juridica.numcgm = entidade.numcgm

           WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."','dd/mm/yyyy'))
             AND teto_remuneratorio.vigencia = ( SELECT MAX(teto_remuneratorio.vigencia)
                                                   FROM tcemg.teto_remuneratorio
                                                  WHERE teto_remuneratorio.vigencia <= last_day(TO_DATE('01/".$this->getDado('mes')."/".$this->getDado('exercicio')."','dd/mm/yyyy'))
                                               )
        GROUP BY tipo_registro
               , vlparateto
               , tipo_cadastro
               , just_alteracao
               , dt_inicial
               , dt_final
               , cnpj
        ";
        return $stSql;
    }

    public function __destruct(){}
}
?>