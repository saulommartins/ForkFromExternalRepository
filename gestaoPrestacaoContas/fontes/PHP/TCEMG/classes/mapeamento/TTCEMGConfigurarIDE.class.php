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
/*
 * Arquivo de mapeamento da tabela tcepb.configurar_ide
 * Data de Criação   : 07/01/2014

 * @author Analista      Eduardo Paculski Schitz
 * @author Desenvolvedor Franver Sarmento de Moraes

 * @package URBEM
 * @subpackage

 * @ignore

  $Id: $
  $Date: $
  $Author: $
  $Rev: $

  $Rev$:
  $Author$:
  $Date$:

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TTCEMGConfigurarIDE extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('tcemg.configurar_ide');
        $this->setCampoCod('exercicio');

        $this->AddCampo('exercicio'           ,'varchar',true  ,4  ,true  ,false);
        $this->AddCampo('cod_municipio'       ,'integer',false ,5  ,false ,false);
        $this->AddCampo('opcao_semestralidade','integer',false ,'' ,false ,false);
    }

    public function montaRecuperaTodos()
    {
        $stSql = "
              SELECT *
                FROM tcemg.configurar_ide
               WHERE exercicio = '".$this->getDado('exercicio')."'
        ";
        return $stSql;
    }

    public function recuperaDadosExportacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDadosExportacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosExportacao()
    {
        $stSql = "
              SELECT configurar_ide.cod_municipio AS codmunicipio
                   , configurar_ide.opcao_semestralidade AS opcaosemestralidade
                   , TO_CHAR(NOW(),'ddmmyyyy') AS datageracao
                   , configurar_ide.exercicio
                   , tabela.cnpj AS cnpjmunicipio
                   , tabela.cod_orgao AS codorgao
                   , tabela.tipo_orgao AS tipoorgao
                   , tabela.exercicio_loa AS exercicioreferencialoa
                   , (SELECT ano_inicio from ppa.ppa WHERE '".Sessao::getExercicio()."' between ano_inicio AND ano_final) AS exercicioinicialppa
                   , (SELECT ano_final from ppa.ppa WHERE '".Sessao::getExercicio()."' between ano_inicio AND ano_final) AS exerciciofinalppa
                   , TO_CHAR(NOW(),'yyyymmdd')||'".Sessao::getExercicio()."'||'".$this->getDado('mes')."' AS codcontroleremessa
                FROM tcemg.configurar_ide
          INNER JOIN (
                      SELECT sw_cgm_pessoa_juridica.cnpj
                           , config_entidade.valor AS cod_orgao
                           , 2 AS tipo_orgao
                           , entidade.exercicio
                           , CASE WHEN entidade.cod_entidade = (SELECT valor
                                                                  FROM administracao.configuracao
                                                                 WHERE parametro = 'cod_entidade_prefeitura'
                                                                   AND exercicio = '".Sessao::getExercicio()."'
                                                               )::integer
                                  THEN (SELECT configuracao_loa.exercicio
                                          FROM normas.norma
                                    INNER JOIN tcemg.configuracao_loa
                                            ON configuracao_loa.cod_norma = norma.cod_norma
                                         WHERE configuracao_loa.exercicio = entidade.exercicio)
                                  ELSE ''
                              END AS exercicio_loa
                           , ppa.exercicio_inicial_ppa
                           , ppa.exercicio_final_ppa
                        FROM sw_cgm_pessoa_juridica
                  INNER JOIN orcamento.entidade
                          ON entidade.numcgm = sw_cgm_pessoa_juridica.numcgm
                  INNER JOIN (
                              SELECT valor
                                   , cod_entidade
                                   , exercicio
                                FROM administracao.configuracao_entidade
                               WHERE parametro = 'tcemg_tipo_orgao_entidade_sicom'
                             ) AS config_entidade
                          ON config_entidade.exercicio = entidade.exercicio
                         AND config_entidade.cod_entidade = entidade.cod_entidade
                  INNER JOIN (
                              SELECT norma.exercicio AS exercicio_inicial_ppa
                                   , SUBSTR(norma_data_termino.dt_termino::varchar,1,4) AS exercicio_final_ppa
                                   , configuracao_leis_ppa.exercicio
                                FROM normas.norma
                          INNER JOIN tcemg.configuracao_leis_ppa
                                  ON configuracao_leis_ppa.cod_norma = norma.cod_norma
                          INNER JOIN normas.norma_data_termino
                                  ON norma_data_termino.cod_norma = norma.cod_norma
                               WHERE configuracao_leis_ppa.tipo_configuracao = 'consulta'
                                 AND configuracao_leis_ppa.status <> 'f'
                             ) ppa
                          ON ppa.exercicio = entidade.exercicio
        ";

        if (count(explode(',',$this->getDado('entidades'))) > 1) {
            $stSql .= "
                       WHERE entidade.cod_entidade = (SELECT valor
                                                        FROM administracao.configuracao
                                                       WHERE parametro = 'cod_entidade_prefeitura'
                                                         AND exercicio = '".Sessao::getExercicio()."'
                                                     )::integer
            ";
        } else {
            $stSql .= "WHERE entidade.cod_entidade = ".$this->getDado('entidades');
        }
        $stSql .= "   ) as tabela
                   ON tabela.exercicio = configurar_ide.exercicio
                WHERE configurar_ide.exercicio = '".Sessao::getExercicio()."'
        ";
        return $stSql;
    }

    public function recuperaDadosExportacaoFolha(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDadosExportacaoFolha",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosExportacaoFolha()
    {
        $stSql = "
              SELECT configurar_ide.cod_municipio AS codmunicipio
                   , configurar_ide.opcao_semestralidade AS opcaosemestralidade
                   , TO_CHAR(NOW(),'ddmmyyyy') AS datageracao
                   , configurar_ide.exercicio
                   , tabela.cnpj AS cnpjmunicipio
                   , tabela.cod_orgao AS codorgao
                   , tabela.tipo_orgao AS tipoorgao
                   , tabela.exercicio_loa AS exercicioreferencialoa
                   , (SELECT ano_inicio from ppa.ppa WHERE '".Sessao::getExercicio()."' between ano_inicio AND ano_final) AS exercicioinicialppa
                   , (SELECT ano_final from ppa.ppa WHERE '".Sessao::getExercicio()."' between ano_inicio AND ano_final) AS exerciciofinalppa
                   , TO_CHAR(NOW(),'yyyymmdd')||'".Sessao::getExercicio()."'||'".$this->getDado('mes')."' AS codcontroleremessa
                FROM tcemg.configurar_ide
          INNER JOIN (
                      SELECT sw_cgm_pessoa_juridica.cnpj
                           , config_entidade.valor AS cod_orgao
                           , 2 AS tipo_orgao
                           , entidade.exercicio
                           , CASE WHEN entidade.cod_entidade = (SELECT valor
                                                                  FROM administracao.configuracao
                                                                 WHERE parametro = 'cod_entidade_prefeitura'
                                                                   AND exercicio = '".Sessao::getExercicio()."'
                                                               )::integer
                                  THEN (SELECT configuracao_loa.exercicio
                                          FROM normas.norma
                                    INNER JOIN tcemg.configuracao_loa
                                            ON configuracao_loa.cod_norma = norma.cod_norma
                                         WHERE configuracao_loa.exercicio = entidade.exercicio)
                                  ELSE ''
                              END AS exercicio_loa
                        FROM sw_cgm_pessoa_juridica
                  INNER JOIN orcamento.entidade
                          ON entidade.numcgm = sw_cgm_pessoa_juridica.numcgm
                  INNER JOIN (
                              SELECT valor
                                   , cod_entidade
                                   , exercicio
                                FROM administracao.configuracao_entidade
                               WHERE parametro = 'tcemg_tipo_orgao_entidade_sicom'
                             ) AS config_entidade
                          ON config_entidade.exercicio = entidade.exercicio
                         AND config_entidade.cod_entidade = entidade.cod_entidade
        ";

        if (count(explode(',',$this->getDado('entidades'))) > 1) {
            $stSql .= "
                       WHERE entidade.cod_entidade = (SELECT valor
                                                        FROM administracao.configuracao
                                                       WHERE parametro = 'cod_entidade_prefeitura'
                                                         AND exercicio = '".Sessao::getExercicio()."'
                                                     )::integer
            ";
        } else {
            $stSql .= "WHERE entidade.cod_entidade IN (".$this->getDado('entidades').")";
        }
        $stSql .= "   ) as tabela
                   ON tabela.exercicio = configurar_ide.exercicio
                WHERE configurar_ide.exercicio = '".Sessao::getExercicio()."'
        ";
        return $stSql;
    }
    
    public function __destruct(){}

}

?>
