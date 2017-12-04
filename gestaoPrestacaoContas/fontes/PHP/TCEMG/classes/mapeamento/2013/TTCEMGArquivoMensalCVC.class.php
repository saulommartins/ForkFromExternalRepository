<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Solu��es em Gest�o P�blica                                *
    * @copyright (c) 2013 Confedera��o Nacional de Munic�pos                         *
    * @author Confedera��o Nacional de Munic�pios                                    *
    *                                                                                *
    * Este programa � software livre; voc� pode redistribu�-lo e/ou modific�-lo  sob *
    * os termos da Licen�a P�blica Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a vers�o 2 da Licen�a, como (a seu crit�rio) qualquer vers�o *
    *                                                                                *
    * Este  programa  �  distribu�do  na  expectativa  de  que  seja  �til,   por�m, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia impl�cita  de  COMERCIABILIDADE  OU *
    * ADEQUA��O A UMA FINALIDADE ESPEC�FICA. Consulte a Licen�a P�blica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Voc� deve ter recebido uma c�pia da Licen�a P�blica Geral  do  GNU  junto  com *
    * este programa; se n�o, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endere�o 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de mapeamento do arquivo CVC.inc.php
    * Data de Cria��o:  27/01/2014

    * @author Analista: Sergio
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCEMGArquivoMensalCVC.class.php 62603 2015-05-22 17:25:16Z carlos.silva $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGArquivoMensalCVC extends Persistente
{
    public function TTCEMGArquivoMensalCVC() {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaVeiculosNovos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaVeiculosNovos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    
    public function montaRecuperaVeiculosNovos()
    {
        $stSql = "
            SELECT registro10.num_unidade
                 , registro10.num_orgao
                 , registro10.exercicio
                 , registro10.cod_veiculo
              FROM (
                          SELECT CASE WHEN descricao_veiculo.num_orgao IS NOT NULL AND descricao_veiculo.num_unidade IS NOT NULL
                                      THEN descricao_veiculo.num_orgao
                                      WHEN descricao_veiculo_locado.cod_orgao IS NOT NULL AND descricao_veiculo_locado.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_locado.cod_orgao
                                      WHEN descricao_veiculo_cessao.cod_orgao IS NOT NULL AND descricao_veiculo_cessao.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_cessao.cod_orgao
                                      ELSE 0
                                  END AS num_orgao
                               , CASE WHEN descricao_veiculo.num_orgao IS NOT NULL AND descricao_veiculo.num_unidade IS NOT NULL
                                      THEN descricao_veiculo.num_unidade
                                      WHEN descricao_veiculo_locado.cod_orgao IS NOT NULL AND descricao_veiculo_locado.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_locado.cod_unidade
                                      WHEN descricao_veiculo_cessao.cod_orgao IS NOT NULL AND descricao_veiculo_cessao.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_cessao.cod_unidade
                                      ELSE 0
                                  END AS num_unidade
                               , CASE WHEN descricao_veiculo.num_orgao IS NOT NULL AND descricao_veiculo.num_unidade IS NOT NULL
                                      THEN descricao_veiculo.exercicio
                                      WHEN descricao_veiculo_locado.cod_orgao IS NOT NULL AND descricao_veiculo_locado.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_locado.exercicio_locacao
                                      WHEN descricao_veiculo_cessao.cod_orgao IS NOT NULL AND descricao_veiculo_cessao.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_cessao.exercicio_cessao
                                      ELSE '0'
                                  END AS exercicio
                               , veiculo.cod_veiculo
                            FROM frota.veiculo
            
                      INNER JOIN frota.modelo
                              ON modelo.cod_modelo = veiculo.cod_modelo
                             AND modelo.cod_marca = veiculo.cod_marca
            
                       LEFT JOIN frota.marca
                              ON marca.cod_marca = modelo.cod_marca
            
                      INNER JOIN tcemg.tipo_veiculo_vinculo
                              ON tipo_veiculo_vinculo.cod_tipo = veiculo.cod_tipo_veiculo
            
                      INNER JOIN frota.veiculo_propriedade
                              ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                             AND veiculo_propriedade.timestamp = ( SELECT MAX(vp.timestamp)
                                                                       FROM frota.veiculo_propriedade as vp
                                                                      WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo
                                                                   )
                       LEFT JOIN ( SELECT bem.descricao
                                        , bem.cod_bem
                                        , bem_comprado.num_orgao
                                        , bem_comprado.num_unidade
                                        , bem_comprado.exercicio
                                        , veiculo_propriedade.cod_veiculo
                                        , MAX(veiculo_propriedade.timestamp) AS timestamp
                                        , bem_comprado.cod_entidade
                                        , bem.dt_aquisicao
                                        , '01'::VARCHAR AS situacao_veiculo
                                     FROM frota.veiculo
                               INNER JOIN frota.veiculo_propriedade
                                       ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                      AND veiculo_propriedade.proprio = true
                               INNER JOIN frota.proprio
                                       ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                                      AND proprio.timestamp = veiculo_propriedade.timestamp
                               INNER JOIN patrimonio.bem
                                       ON bem.cod_bem = proprio.cod_bem
                               INNER JOIN patrimonio.bem_comprado
                                       ON bem_comprado.cod_bem = bem.cod_bem
                                 GROUP BY bem.descricao
                                        , veiculo_propriedade.cod_veiculo
                                        , bem.cod_bem
                                        , bem_comprado.cod_entidade
                                        , bem_comprado.num_orgao
                                        , bem_comprado.num_unidade
                                        , bem_comprado.exercicio
                                        , veiculo.dt_aquisicao
                                         ) AS descricao_veiculo
                             ON  descricao_veiculo.cod_veiculo = veiculo_propriedade.cod_veiculo
                            AND descricao_veiculo.timestamp = veiculo_propriedade.timestamp
                            
                      LEFT JOIN ( SELECT CASE WHEN despesa.num_orgao IS NOT NULL AND despesa.num_unidade IS NOT NULL
                                               THEN despesa.num_orgao
                                               ELSE veiculo_uniorcam.num_orgao
                                           END AS cod_orgao
                                        , CASE WHEN despesa.num_orgao IS NOT NULL AND despesa.num_unidade IS NOT NULL
                                               THEN despesa.num_unidade
                                               ELSE veiculo_uniorcam.num_unidade
                                           END AS cod_unidade
                                        , CASE WHEN despesa.num_orgao IS NOT NULL AND despesa.num_unidade IS NOT NULL
                                               THEN despesa.exercicio
                                               ELSE veiculo_uniorcam.exercicio
                                               
                                           END AS exercicio_locacao
                                        , veiculo_propriedade.cod_veiculo
                                        , MAX(veiculo_propriedade.timestamp) AS timestamp
                                        , veiculo_locacao.cod_entidade
                                        , veiculo_locacao.dt_inicio
                                        , veiculo_locacao.dt_termino
                                        , '02'::VARCHAR AS situacao_veiculo
                                     FROM frota.veiculo
                               INNER JOIN frota.veiculo_propriedade
                                       ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                      AND veiculo_propriedade.proprio = false
                               INNER JOIN frota.terceiros
                                       ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                                      AND terceiros.timestamp = veiculo_propriedade.timestamp
                                LEFT JOIN frota.veiculo_locacao
                                       ON veiculo_locacao.cod_veiculo = veiculo.cod_veiculo
                                LEFT JOIN patrimonio.veiculo_uniorcam
                                       ON veiculo.cod_veiculo = veiculo_uniorcam.cod_veiculo
                                LEFT JOIN empenho.empenho
                                       ON empenho.exercicio    = veiculo_locacao.exercicio
                                      AND empenho.cod_entidade = veiculo_locacao.cod_entidade
                                      AND empenho.cod_empenho  = veiculo_locacao.cod_empenho
                                LEFT JOIN empenho.pre_empenho
                                       ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                      AND pre_empenho.exercicio       = empenho.exercicio
                                LEFT JOIN empenho.pre_empenho_despesa
                                       ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                      AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                                LEFT JOIN orcamento.despesa
                                       ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                      AND despesa.exercicio  = pre_empenho_despesa.exercicio
                                 GROUP BY cod_unidade
                                        , cod_orgao
                                        , veiculo_propriedade.cod_veiculo
                                        , veiculo_locacao.cod_entidade
                                        , veiculo_uniorcam.cod_veiculo
                                        , veiculo_locacao.dt_inicio
                                        , veiculo_locacao.dt_termino
                                        , exercicio_locacao
                                         ) AS descricao_veiculo_locado
                                ON  descricao_veiculo_locado.cod_veiculo = veiculo_propriedade.cod_veiculo
                               AND  descricao_veiculo_locado.timestamp = veiculo_propriedade.timestamp
            
                       LEFT JOIN ( SELECT veiculo_uniorcam.num_orgao AS cod_orgao
                                        , veiculo_uniorcam.num_unidade AS cod_unidade
                                        , veiculo_uniorcam.exercicio AS exercicio_cessao
                                        , veiculo_propriedade.cod_veiculo
                                        , MAX(veiculo_propriedade.timestamp) AS timestamp
                                        , veiculo_cessao.dt_inicio
                                        , veiculo_cessao.dt_termino
                                        , '03'::VARCHAR AS situacao_veiculo
                                     FROM frota.veiculo
                               INNER JOIN frota.veiculo_propriedade
                                       ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                      AND veiculo_propriedade.proprio = false
                               INNER JOIN frota.terceiros
                                       ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                                      AND terceiros.timestamp = veiculo_propriedade.timestamp
                                LEFT JOIN frota.veiculo_cessao
                                       ON veiculo_cessao.cod_veiculo = veiculo.cod_veiculo
                                LEFT JOIN patrimonio.veiculo_uniorcam
                                       ON veiculo.cod_veiculo = veiculo_uniorcam.cod_veiculo
                                 GROUP BY cod_unidade
                                        , cod_orgao
                                        , veiculo_propriedade.cod_veiculo
                                        , veiculo_uniorcam.cod_veiculo
                                        , veiculo_cessao.dt_inicio
                                        , veiculo_cessao.dt_termino
                                        , exercicio_cessao
                                ) AS descricao_veiculo_cessao
                             ON descricao_veiculo_cessao.cod_veiculo = veiculo_propriedade.cod_veiculo
                            AND descricao_veiculo_cessao.timestamp = veiculo_propriedade.timestamp
                          WHERE descricao_veiculo.dt_aquisicao     BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                             OR descricao_veiculo_locado.dt_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                             OR descricao_veiculo_cessao.dt_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') 
                       ORDER BY veiculo.cod_veiculo
                   ) AS registro10
         LEFT JOIN tcemg.arquivo_cvc
                ON registro10.num_unidade = arquivo_cvc.num_unidade
               AND registro10.num_orgao   = arquivo_cvc.num_orgao
               AND registro10.exercicio   = arquivo_cvc.exercicio
               AND registro10.cod_veiculo = arquivo_cvc.cod_veiculo
             WHERE arquivo_cvc.cod_veiculo IS NULL
               AND (registro10.num_unidade > 0 AND registro10.num_orgao > 0)
               AND registro10.exercicio::INTEGER <= ".Sessao::getExercicio()."
        ";
        return $stSql;
    }
    
    public function recuperaVeiculos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao=""){
        return $this->executaRecupera("montaRecuperaVeiculos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaVeiculos()
    {
        $stSql = "
            SELECT *
              FROM (
                          SELECT 10 AS tipo_registro
                               , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao
                               , CASE WHEN descricao_veiculo.num_orgao IS NOT NULL AND descricao_veiculo.num_unidade IS NOT NULL
                                      THEN descricao_veiculo.num_orgao
                                      WHEN descricao_veiculo_locado.cod_orgao IS NOT NULL AND descricao_veiculo_locado.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_locado.cod_orgao
                                      WHEN descricao_veiculo_cessao.cod_orgao IS NOT NULL AND descricao_veiculo_cessao.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_cessao.cod_orgao
                                      ELSE 0
                                  END AS num_orgao
                               , CASE WHEN descricao_veiculo.num_orgao IS NOT NULL AND descricao_veiculo.num_unidade IS NOT NULL
                                      THEN descricao_veiculo.num_unidade
                                      WHEN descricao_veiculo_locado.cod_orgao IS NOT NULL AND descricao_veiculo_locado.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_locado.cod_unidade
                                      WHEN descricao_veiculo_cessao.cod_orgao IS NOT NULL AND descricao_veiculo_cessao.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_cessao.cod_unidade
                                      ELSE 0
                                  END AS num_unidade
                               , CASE WHEN descricao_veiculo.num_orgao IS NOT NULL AND descricao_veiculo.num_unidade IS NOT NULL
                                      THEN descricao_veiculo.exercicio
                                      WHEN descricao_veiculo_locado.cod_orgao IS NOT NULL AND descricao_veiculo_locado.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_locado.exercicio_locacao
                                      WHEN descricao_veiculo_cessao.cod_orgao IS NOT NULL AND descricao_veiculo_cessao.cod_unidade IS NOT NULL
                                      THEN descricao_veiculo_cessao.exercicio_cessao
                                      ELSE '0'
                                  END AS exercicio
                               , CASE WHEN (descricao_veiculo.num_orgao IS NOT NULL AND descricao_veiculo.num_unidade IS NOT NULL)
                                      THEN LPAD(LPAD(descricao_veiculo.num_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo.num_unidade::VARCHAR, 2, '0'), 5, '0')
                                      WHEN descricao_veiculo_locado.cod_orgao IS NOT NULL AND descricao_veiculo_locado.cod_unidade IS NOT NULL
                                      THEN LPAD(LPAD(descricao_veiculo_locado.cod_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo_locado.cod_unidade::VARCHAR, 2, '0'), 5, '0')
                                      WHEN descricao_veiculo_cessao.cod_orgao IS NOT NULL AND descricao_veiculo_cessao.cod_unidade IS NOT NULL
                                      THEN LPAD(LPAD(descricao_veiculo_cessao.cod_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo_cessao.cod_unidade::VARCHAR, 2, '0'), 5, '0')
                                      ELSE LPAD('0', 5, '0')
                                  END AS cod_unidade_sub
                               , veiculo.cod_veiculo
                               , tipo_veiculo_vinculo.cod_tipo_tce AS tipo_veiculo
                               , tipo_veiculo_vinculo.cod_subtipo_tce AS subtipo_veiculo
                               , CASE WHEN veiculo_propriedade.proprio IS TRUE
                                      THEN descricao_veiculo.descricao
                                      ELSE modelo.nom_modelo || ' ' || veiculo.ano_fabricacao || ' | COR : ' || veiculo.cor || ' | PLACA : ' || veiculo.placa
                                  END AS descricao
                               , marca.nom_marca AS marca
                               , modelo.nom_modelo AS modelo
                               , veiculo.ano_fabricacao
                               , CASE WHEN tipo_veiculo_vinculo.cod_tipo_tce = 3
                                      THEN SUBSTR(veiculo.placa, 1, 3) || ' ' || SUBSTR(veiculo.placa, 4, 4)
                                      ELSE ''::VARCHAR
                                  END AS placa
                               , CASE WHEN tipo_veiculo_vinculo.cod_tipo_tce = 3
                                      THEN veiculo.chassi
                                      ELSE ''::VARCHAR
                                  END AS chassi
                               , CASE WHEN tipo_veiculo_vinculo.cod_tipo_tce = 3
                                      THEN veiculo.num_certificado
                                      ELSE ''::VARCHAR
                                  END AS numero_renavam
                               , '' AS numero_serie
                               , CASE WHEN descricao_veiculo_locado.cod_veiculo IS NOT NULL
                                      THEN descricao_veiculo_locado.situacao_veiculo
                                      WHEN descricao_veiculo_cessao.cod_veiculo IS NOT NULL
                                      THEN descricao_veiculo_cessao.situacao_veiculo
                                      WHEN descricao_veiculo.cod_veiculo IS NOT NULL
                                      THEN descricao_veiculo.situacao_veiculo
                                      ELSE '0'
                                  END AS situacao_veiculo
                               , CASE WHEN descricao_veiculo.cod_veiculo IS NOT NULL
                                      THEN ' '
                                      ELSE '2'
                                  END AS tipo_documento
                               , CASE WHEN descricao_veiculo_locado.cod_veiculo IS NOT NULL
                                      THEN descricao_veiculo_locado.cnpj
                                      WHEN descricao_veiculo_cessao.cod_veiculo IS NOT NULL
                                      THEN descricao_veiculo_cessao.cnpj
                                      ELSE ' '
                                  END AS nro_documento
                               , 01 AS tipo_deslocamento
            
                            FROM frota.veiculo
            
                      INNER JOIN frota.modelo
                              ON modelo.cod_modelo = veiculo.cod_modelo
                             AND modelo.cod_marca = veiculo.cod_marca
            
                       LEFT JOIN frota.marca
                              ON marca.cod_marca = modelo.cod_marca
            
                      INNER JOIN tcemg.tipo_veiculo_vinculo
                              ON tipo_veiculo_vinculo.cod_tipo = veiculo.cod_tipo_veiculo
            
                      INNER JOIN frota.veiculo_propriedade
                              ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                             AND veiculo_propriedade.timestamp = ( SELECT MAX(vp.timestamp)
                                                                       FROM frota.veiculo_propriedade as vp
                                                                      WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo )
                       LEFT JOIN ( SELECT bem.descricao
                                        , bem.cod_bem
                                        , bem_comprado.num_orgao
                                        , bem_comprado.num_unidade
                                        , bem_comprado.exercicio
                                        , veiculo_propriedade.cod_veiculo
                                        , MAX(veiculo_propriedade.timestamp) AS timestamp
                                        , bem_comprado.cod_entidade
                                        , bem.dt_aquisicao
                                        , '01'::VARCHAR AS situacao_veiculo
                                     FROM frota.veiculo
                               INNER JOIN frota.veiculo_propriedade
                                       ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                      AND veiculo_propriedade.proprio = true
                               INNER JOIN frota.proprio
                                       ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                                      AND proprio.timestamp = veiculo_propriedade.timestamp
                               INNER JOIN patrimonio.bem
                                       ON bem.cod_bem = proprio.cod_bem
                               INNER JOIN patrimonio.bem_comprado
                                       ON bem_comprado.cod_bem = bem.cod_bem
                                 GROUP BY bem.descricao
                                        , veiculo_propriedade.cod_veiculo
                                        , bem.cod_bem
                                        , bem_comprado.cod_entidade
                                        , bem_comprado.num_orgao
                                        , bem_comprado.num_unidade
                                        , bem_comprado.exercicio
                                        , veiculo.dt_aquisicao
                                         ) AS descricao_veiculo
                             ON  descricao_veiculo.cod_veiculo = veiculo_propriedade.cod_veiculo
                            AND descricao_veiculo.timestamp = veiculo_propriedade.timestamp
                            
                      LEFT JOIN ( SELECT CASE WHEN despesa.num_orgao IS NOT NULL AND despesa.num_unidade IS NOT NULL
                                               THEN despesa.num_orgao
                                               ELSE veiculo_uniorcam.num_orgao
                                           END AS cod_orgao
                                        , CASE WHEN despesa.num_orgao IS NOT NULL AND despesa.num_unidade IS NOT NULL
                                               THEN despesa.num_unidade
                                               ELSE veiculo_uniorcam.num_unidade
                                           END AS cod_unidade
                                        , CASE WHEN despesa.num_orgao IS NOT NULL AND despesa.num_unidade IS NOT NULL
                                               THEN despesa.exercicio
                                               ELSE veiculo_uniorcam.exercicio
                                           END AS exercicio_locacao
                                        , veiculo_propriedade.cod_veiculo
                                        , MAX(veiculo_propriedade.timestamp) AS timestamp
                                        , veiculo_locacao.cod_entidade
                                        , veiculo_locacao.dt_inicio
                                        , veiculo_locacao.dt_termino
                                        , sw_cgm_pessoa_juridica.cnpj
                                        , '02'::VARCHAR AS situacao_veiculo
                                     FROM frota.veiculo
                               INNER JOIN frota.veiculo_propriedade
                                       ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                      AND veiculo_propriedade.proprio = false
                               INNER JOIN frota.terceiros
                                       ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                                      AND terceiros.timestamp = veiculo_propriedade.timestamp
                                LEFT JOIN frota.veiculo_locacao
                                       ON veiculo_locacao.cod_veiculo = veiculo.cod_veiculo
                                LEFT JOIN patrimonio.veiculo_uniorcam
                                       ON veiculo.cod_veiculo = veiculo_uniorcam.cod_veiculo
                                LEFT JOIN empenho.empenho
                                       ON empenho.exercicio    = veiculo_locacao.exercicio
                                      AND empenho.cod_entidade = veiculo_locacao.cod_entidade
                                      AND empenho.cod_empenho  = veiculo_locacao.cod_empenho
                                LEFT JOIN empenho.pre_empenho
                                       ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                      AND pre_empenho.exercicio       = empenho.exercicio
                                LEFT JOIN empenho.pre_empenho_despesa
                                       ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                                      AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                                LEFT JOIN orcamento.despesa
                                       ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                                      AND despesa.exercicio  = pre_empenho_despesa.exercicio
                                LEFT JOIN sw_cgm_pessoa_juridica
                                       ON sw_cgm_pessoa_juridica.numcgm = veiculo_locacao.cgm_locatario
                                 GROUP BY cod_unidade
                                        , cod_orgao
                                        , veiculo_propriedade.cod_veiculo
                                        , veiculo_locacao.cod_entidade
                                        , veiculo_uniorcam.cod_veiculo
                                        , veiculo_locacao.dt_inicio
                                        , veiculo_locacao.dt_termino
                                        , exercicio_locacao
                                        , sw_cgm_pessoa_juridica.cnpj
                                         ) AS descricao_veiculo_locado
                                ON  descricao_veiculo_locado.cod_veiculo = veiculo_propriedade.cod_veiculo
                               AND  descricao_veiculo_locado.timestamp = veiculo_propriedade.timestamp
            
                       LEFT JOIN ( SELECT veiculo_uniorcam.num_orgao AS cod_orgao
                                        , veiculo_uniorcam.num_unidade AS cod_unidade
                                        , veiculo_uniorcam.exercicio AS exercicio_cessao
                                        , veiculo_propriedade.cod_veiculo
                                        , MAX(veiculo_propriedade.timestamp) AS timestamp
                                        , veiculo_cessao.dt_inicio
                                        , veiculo_cessao.dt_termino
                                        , sw_cgm_pessoa_juridica.cnpj
                                        , '03'::VARCHAR AS situacao_veiculo
                                     FROM frota.veiculo
                               INNER JOIN frota.veiculo_propriedade
                                       ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                                      AND veiculo_propriedade.proprio = false
                               INNER JOIN frota.terceiros
                                       ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                                      AND terceiros.timestamp = veiculo_propriedade.timestamp
                                LEFT JOIN frota.veiculo_cessao
                                       ON veiculo_cessao.cod_veiculo = veiculo.cod_veiculo
                                LEFT JOIN patrimonio.veiculo_uniorcam
                                       ON veiculo.cod_veiculo = veiculo_uniorcam.cod_veiculo
                                LEFT JOIN sw_cgm_pessoa_juridica
                                       ON sw_cgm_pessoa_juridica.numcgm = veiculo_cessao.cgm_cedente
                                 GROUP BY cod_unidade
                                        , cod_orgao
                                        , veiculo_propriedade.cod_veiculo
                                        , veiculo_uniorcam.cod_veiculo
                                        , veiculo_cessao.dt_inicio
                                        , veiculo_cessao.dt_termino
                                        , exercicio_cessao
                                        , sw_cgm_pessoa_juridica.cnpj
                                ) AS descricao_veiculo_cessao
                             ON descricao_veiculo_cessao.cod_veiculo = veiculo_propriedade.cod_veiculo
                            AND descricao_veiculo_cessao.timestamp = veiculo_propriedade.timestamp
            
                     INNER JOIN administracao.configuracao_entidade
                             ON configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
                            AND configuracao_entidade.cod_modulo   = 55
                            AND configuracao_entidade.exercicio    = '".Sessao::getExercicio()."'
                            AND configuracao_entidade.cod_entidade = ".$this->getDado('entidades')."
                          WHERE descricao_veiculo.dt_aquisicao     BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                             OR descricao_veiculo_locado.dt_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                             OR descricao_veiculo_cessao.dt_inicio BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy') 
                       ORDER BY veiculo.cod_veiculo
                    ) AS registro10
            LEFT JOIN tcemg.arquivo_cvc
                   ON registro10.num_unidade = arquivo_cvc.num_unidade
                  AND registro10.num_orgao   = arquivo_cvc.num_orgao
                  --AND registro10.exercicio   = arquivo_cvc.exercicio
                  AND registro10.cod_veiculo = arquivo_cvc.cod_veiculo
                WHERE arquivo_cvc.mes = TO_CHAR(TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy'),'mm')
                  AND arquivo_cvc.cod_veiculo IS NULL 
                
             ORDER BY registro10.cod_veiculo
        ";

        return $stSql;
    }

    public function recuperaGastosVeiculos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaGastosVeiculos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaGastosVeiculos()
    {
        $stSql = "
                  SELECT 20 AS tipo_registro
                       , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao
                       , CASE WHEN (descricao_veiculo.num_orgao IS NOT NULL AND descricao_veiculo.num_unidade IS NOT NULL)
                              THEN LPAD(LPAD(descricao_veiculo.num_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo.num_unidade::VARCHAR, 2, '0'), 5, '0')
                              WHEN descricao_veiculo_locado.cod_orgao IS NOT NULL AND descricao_veiculo_locado.cod_unidade IS NOT NULL
                              THEN LPAD(LPAD(descricao_veiculo_locado.cod_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo_locado.cod_unidade::VARCHAR, 2, '0'), 5, '0')
                              WHEN descricao_veiculo_cessao.cod_orgao IS NOT NULL AND descricao_veiculo_cessao.cod_unidade IS NOT NULL
                              THEN LPAD(LPAD(descricao_veiculo_cessao.cod_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo_cessao.cod_unidade::VARCHAR, 2, '0'), 5, '0')
                              ELSE LPAD('0', 5, '0')
                          END AS cod_unidade_sub
                       , manutencao.cod_veiculo
                       , CASE WHEN (manutencao.cod_manutencao = manutencao_empenho.cod_manutencao AND manutencao.exercicio = manutencao_empenho.exercicio)
                              THEN CASE WHEN posto.interno IS TRUE
                                        THEN 1
                                        ELSE 2
                                    END
                              ELSE 1
                          END AS origem_gasto
                       , CASE WHEN (restos_pre_empenho.num_orgao IS NOT NULL AND restos_pre_empenho.num_unidade IS NOT NULL)
                              THEN LPAD(LPAD(restos_pre_empenho.num_orgao::VARCHAR, 2, '0')||LPAD(restos_pre_empenho.num_unidade::VARCHAR, 2, '0'), 5, '0')
                              WHEN despesa.num_orgao IS NOT NULL AND despesa.num_unidade IS NOT NULL
                              THEN LPAD(LPAD(despesa.num_orgao::VARCHAR, 2, '0')||LPAD(despesa.num_unidade::VARCHAR, 2, '0'), 5, '0')
                              ELSE ''
                          END AS cod_unidade_subempenho
                       , manutencao_empenho.cod_empenho AS nro_empenho
                       , TO_CHAR(empenho.dt_empenho::timestamp,'ddmmyyyy') AS dt_empenho
                       , COALESCE((SELECT COALESCE (km,0) AS km
                                     FROM frota.manutencao AS kmini
                                     --DATAINICIO
                                    WHERE kmini.dt_manutencao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                      AND kmini.cod_veiculo    = manutencao.cod_veiculo
                                      AND km > 0
                                 ORDER BY km ASC LIMIT 1
                                  ),0) AS marcacao_inicial
    
                       , COALESCE((SELECT  coalesce(km,0) AS km
                                     FROM frota.manutencao kmfim
                                    WHERE kmfim.dt_manutencao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy') AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                                      AND kmfim.cod_veiculo    = manutencao.cod_veiculo
                                      AND km > 0
                                 ORDER BY km DESC LIMIT 1
                                  ),0) marcacao_final
    
                       , CASE item.cod_tipo
                              WHEN 2 THEN 08
                              WHEN 3 THEN 09
                              WHEN 4 THEN 99
                              ELSE item.cod_tipo
                          END AS tipo_gasto
                        , TRIM(REPLACE(TO_CHAR(manutencao_item.quantidade, '999999999.9999'),'.',',')) AS qtde_utilizada
                        , manutencao_item.valor AS vl_gasto 
                        , CASE WHEN (item.cod_tipo = 2
                                  OR item.cod_tipo = 3
                                  OR item.cod_tipo = 4)
                               THEN catalogo_item.descricao_resumida
                               ELSE ''
                           END AS dsc_pecas_servicos
                        , 2 AS atestado_controle
    
                    FROM frota.manutencao
              INNER JOIN frota.veiculo
                      ON veiculo.cod_veiculo = manutencao.cod_veiculo
              
              INNER JOIN frota.manutencao_item
                      ON manutencao_item.exercicio      = manutencao.exercicio
                     AND manutencao_item.cod_manutencao = manutencao.cod_manutencao
              
              INNER JOIN frota.item
                      ON item.cod_item = manutencao_item.cod_item
              
               LEFT JOIN almoxarifado.catalogo_item
                      ON catalogo_item.cod_item = manutencao_item.cod_item
              
               LEFT JOIN frota.efetivacao
                      ON efetivacao.cod_manutencao = manutencao.cod_manutencao
                     AND manutencao.exercicio      = efetivacao.exercicio_manutencao
    
               LEFT JOIN frota.autorizacao
                      ON autorizacao.cod_autorizacao = efetivacao.cod_autorizacao
                     AND autorizacao.exercicio       = efetivacao.exercicio_autorizacao
    
               LEFT JOIN frota.posto
                      ON posto.cgm_posto = autorizacao.cgm_fornecedor
                      
               LEFT JOIN frota.manutencao_empenho
                      ON manutencao.cod_manutencao = manutencao_empenho.cod_manutencao
                     AND manutencao.exercicio      = manutencao_empenho.exercicio
                     
               LEFT JOIN empenho.empenho
                      ON manutencao_empenho.cod_entidade      = empenho.cod_entidade
                     AND manutencao_empenho.cod_empenho       = empenho.cod_empenho
                     AND manutencao_empenho.exercicio_empenho = empenho.exercicio
    
               LEFT JOIN empenho.pre_empenho
                      ON pre_empenho.exercicio       = empenho.exercicio
                     AND pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
    
               LEFT JOIN empenho.pre_empenho_despesa
                      ON pre_empenho_despesa.exercicio       = empenho.exercicio
                     AND pre_empenho_despesa.cod_pre_empenho = empenho.cod_pre_empenho
               
               LEFT JOIN orcamento.despesa
                      ON despesa.exercicio   = pre_empenho_despesa.exercicio
                     AND despesa.cod_despesa = pre_empenho_despesa.cod_despesa
               
               LEFT JOIN empenho.restos_pre_empenho
                      ON restos_pre_empenho.exercicio       = empenho.exercicio
                     AND restos_pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
    
              INNER JOIN frota.veiculo_propriedade
                      ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                     AND veiculo_propriedade.timestamp = ( SELECT MAX(vp.timestamp)
                                                             FROM frota.veiculo_propriedade as vp
                                                            WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo )
    
               LEFT JOIN ( SELECT bem.descricao
                                , bem.cod_bem
                                , bem_comprado.num_orgao
                                , bem_comprado.num_unidade
                                , bem_comprado.exercicio
                                , veiculo_propriedade.cod_veiculo
                                , MAX(veiculo_propriedade.timestamp) AS timestamp
                                , bem_comprado.cod_entidade
                                , bem.dt_aquisicao
                                , '01'::VARCHAR AS situacao_veiculo
                             FROM frota.veiculo
                       INNER JOIN frota.veiculo_propriedade
                               ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                              AND veiculo_propriedade.proprio = true
                       INNER JOIN frota.proprio
                               ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                              AND proprio.timestamp = veiculo_propriedade.timestamp
                       INNER JOIN patrimonio.bem
                               ON bem.cod_bem = proprio.cod_bem
                       INNER JOIN patrimonio.bem_comprado
                               ON bem_comprado.cod_bem = bem.cod_bem
                         GROUP BY bem.descricao
                                , veiculo_propriedade.cod_veiculo
                                , bem.cod_bem
                                , bem_comprado.cod_entidade
                                , bem_comprado.num_orgao
                                , bem_comprado.num_unidade
                                , bem_comprado.exercicio
                                , veiculo.dt_aquisicao
                                 ) AS descricao_veiculo
                     ON  descricao_veiculo.cod_veiculo = veiculo_propriedade.cod_veiculo
                    AND descricao_veiculo.timestamp = veiculo_propriedade.timestamp
                    
              LEFT JOIN ( SELECT CASE WHEN despesa.num_orgao IS NOT NULL AND despesa.num_unidade IS NOT NULL
                                       THEN despesa.num_orgao
                                       ELSE veiculo_uniorcam.num_orgao
                                   END AS cod_orgao
                                , CASE WHEN despesa.num_orgao IS NOT NULL AND despesa.num_unidade IS NOT NULL
                                       THEN despesa.num_unidade
                                       ELSE veiculo_uniorcam.num_unidade
                                   END AS cod_unidade
                                , CASE WHEN despesa.num_orgao IS NOT NULL AND despesa.num_unidade IS NOT NULL
                                       THEN despesa.exercicio
                                       ELSE veiculo_uniorcam.exercicio
                                       
                                   END AS exercicio_locacao
                                , veiculo_propriedade.cod_veiculo
                                , MAX(veiculo_propriedade.timestamp) AS timestamp
                                , veiculo_locacao.cod_entidade
                                , veiculo_locacao.dt_inicio
                                , veiculo_locacao.dt_termino
                                , '02'::VARCHAR AS situacao_veiculo
                             FROM frota.veiculo
                       INNER JOIN frota.veiculo_propriedade
                               ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                              AND veiculo_propriedade.proprio = false
                       INNER JOIN frota.terceiros
                               ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                              AND terceiros.timestamp = veiculo_propriedade.timestamp
                        LEFT JOIN frota.veiculo_locacao
                               ON veiculo_locacao.cod_veiculo = veiculo.cod_veiculo
                        LEFT JOIN patrimonio.veiculo_uniorcam
                               ON veiculo.cod_veiculo = veiculo_uniorcam.cod_veiculo
                        LEFT JOIN empenho.empenho
                               ON empenho.exercicio    = veiculo_locacao.exercicio
                              AND empenho.cod_entidade = veiculo_locacao.cod_entidade
                              AND empenho.cod_empenho  = veiculo_locacao.cod_empenho
                        LEFT JOIN empenho.pre_empenho
                               ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                              AND pre_empenho.exercicio       = empenho.exercicio
                        LEFT JOIN empenho.pre_empenho_despesa
                               ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                              AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                        LEFT JOIN orcamento.despesa
                               ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                              AND despesa.exercicio  = pre_empenho_despesa.exercicio
                         GROUP BY cod_unidade
                                , cod_orgao
                                , veiculo_propriedade.cod_veiculo
                                , veiculo_locacao.cod_entidade
                                , veiculo_uniorcam.cod_veiculo
                                , veiculo_locacao.dt_inicio
                                , veiculo_locacao.dt_termino
                                , exercicio_locacao
                                 ) AS descricao_veiculo_locado
                        ON  descricao_veiculo_locado.cod_veiculo = veiculo_propriedade.cod_veiculo
                       AND  descricao_veiculo_locado.timestamp = veiculo_propriedade.timestamp
    
               LEFT JOIN ( SELECT veiculo_uniorcam.num_orgao AS cod_orgao
                                , veiculo_uniorcam.num_unidade AS cod_unidade
                                , veiculo_uniorcam.exercicio AS exercicio_cessao
                                , veiculo_propriedade.cod_veiculo
                                , MAX(veiculo_propriedade.timestamp) AS timestamp
                                , veiculo_cessao.dt_inicio
                                , veiculo_cessao.dt_termino
                                , '03'::VARCHAR AS situacao_veiculo
                             FROM frota.veiculo
                       INNER JOIN frota.veiculo_propriedade
                               ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                              AND veiculo_propriedade.proprio = false
                       INNER JOIN frota.terceiros
                               ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                              AND terceiros.timestamp = veiculo_propriedade.timestamp
                        LEFT JOIN frota.veiculo_cessao
                               ON veiculo_cessao.cod_veiculo = veiculo.cod_veiculo
                        LEFT JOIN patrimonio.veiculo_uniorcam
                               ON veiculo.cod_veiculo = veiculo_uniorcam.cod_veiculo
                         GROUP BY cod_unidade
                                , cod_orgao
                                , veiculo_propriedade.cod_veiculo
                                , veiculo_uniorcam.cod_veiculo
                                , veiculo_cessao.dt_inicio
                                , veiculo_cessao.dt_termino
                                , exercicio_cessao
                        ) AS descricao_veiculo_cessao
                     ON descricao_veiculo_cessao.cod_veiculo = veiculo_propriedade.cod_veiculo
                    AND descricao_veiculo_cessao.timestamp = veiculo_propriedade.timestamp
    
             INNER JOIN administracao.configuracao_entidade
                     ON configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
                    AND configuracao_entidade.cod_modulo   = 55
                    AND configuracao_entidade.exercicio    = '".Sessao::getExercicio()."'
                    AND configuracao_entidade.cod_entidade = ".$this->getDado('entidades')."
    
                   WHERE manutencao.dt_manutencao BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                      AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                     AND manutencao.exercicio = '".Sessao::getExercicio()."'
                     AND empenho.dt_empenho <= TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                GROUP BY tipo_registro
                       , LPAD(configuracao_entidade.valor,2,'0')
                       , cod_unidade_sub
                       , manutencao.cod_veiculo
                       , origem_gasto
                       , cod_unidade_subempenho
                       , nro_empenho
                       , empenho.dt_empenho
                       , tipo_gasto
                       , qtde_utilizada
                       , vl_gasto
                       , dsc_pecas_servicos
                       , marcacao_inicial
                       , marcacao_final
                ORDER BY manutencao.cod_veiculo
                       , cod_unidade_subempenho
                       , nro_empenho
        ";
              
        return $stSql;
    }
    
    public function recuperaCVC30(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaCVC30",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaCVC30()
    {
        $stSql = "
              SELECT 30 AS tipo_registro
                   , LPAD(configuracao_entidade.valor,2,'0') AS cod_orgao
                   , CASE WHEN (descricao_veiculo.num_orgao IS NOT NULL AND descricao_veiculo.num_unidade IS NOT NULL)
                          THEN LPAD(LPAD(descricao_veiculo.num_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo.num_unidade::VARCHAR, 2, '0'), 5, '0')
                          WHEN descricao_veiculo_locado.cod_orgao IS NOT NULL AND descricao_veiculo_locado.cod_unidade IS NOT NULL
                          THEN LPAD(LPAD(descricao_veiculo_locado.cod_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo_locado.cod_unidade::VARCHAR, 2, '0'), 5, '0')
                          WHEN descricao_veiculo_cessao.cod_orgao IS NOT NULL AND descricao_veiculo_cessao.cod_unidade IS NOT NULL
                          THEN LPAD(LPAD(descricao_veiculo_cessao.cod_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo_cessao.cod_unidade::VARCHAR, 2, '0'), 5, '0')
                          ELSE LPAD('0', 5, '0')
                      END AS cod_unidade_sub
                   , transporte_escolar.cod_veiculo
                   , sw_cgm.nom_cgm as nome_estabelecimento
                   , sw_cgm.bairro as localidade
                   , transporte_escolar.dias_rodados as qtde_dias_rodados
                   , transporte_escolar.distancia as distacia_estabelecimento
                   , transporte_escolar.passageiros as numero_passageiros
                   , transporte_escolar.cod_turno as turnos
                FROM frota.transporte_escolar
          INNER JOIN frota.veiculo
                  ON veiculo.cod_veiculo = transporte_escolar.cod_veiculo
          INNER JOIN sw_cgm
                  ON sw_cgm.numcgm = transporte_escolar.cgm_escola

          INNER JOIN frota.veiculo_propriedade
                  ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                 AND veiculo_propriedade.timestamp = ( SELECT MAX(vp.timestamp)
                                                         FROM frota.veiculo_propriedade as vp
                                                        WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo )

           LEFT JOIN ( SELECT bem.descricao
                            , bem.cod_bem
                            , bem_comprado.num_orgao
                            , bem_comprado.num_unidade
                            , bem_comprado.exercicio
                            , veiculo_propriedade.cod_veiculo
                            , MAX(veiculo_propriedade.timestamp) AS timestamp
                            , bem_comprado.cod_entidade
                            , bem.dt_aquisicao
                            , '01'::VARCHAR AS situacao_veiculo
                         FROM frota.veiculo
                   INNER JOIN frota.veiculo_propriedade
                           ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                          AND veiculo_propriedade.proprio = true
                   INNER JOIN frota.proprio
                           ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                          AND proprio.timestamp = veiculo_propriedade.timestamp
                   INNER JOIN patrimonio.bem
                           ON bem.cod_bem = proprio.cod_bem
                   INNER JOIN patrimonio.bem_comprado
                           ON bem_comprado.cod_bem = bem.cod_bem
                     GROUP BY bem.descricao
                            , veiculo_propriedade.cod_veiculo
                            , bem.cod_bem
                            , bem_comprado.cod_entidade
                            , bem_comprado.num_orgao
                            , bem_comprado.num_unidade
                            , bem_comprado.exercicio
                            , veiculo.dt_aquisicao
                             ) AS descricao_veiculo
                 ON  descricao_veiculo.cod_veiculo = veiculo_propriedade.cod_veiculo
                AND descricao_veiculo.timestamp = veiculo_propriedade.timestamp
                
          LEFT JOIN ( SELECT CASE WHEN veiculo_uniorcam.num_orgao IS NOT NULL AND veiculo_uniorcam.num_unidade IS NOT NULL
                                   THEN veiculo_uniorcam.num_orgao
                                   ELSE despesa.num_orgao
                               END AS cod_orgao
                            , CASE WHEN veiculo_uniorcam.num_orgao IS NOT NULL AND veiculo_uniorcam.num_unidade IS NOT NULL
                                   THEN veiculo_uniorcam.num_unidade
                                   ELSE despesa.num_unidade
                               END AS cod_unidade
                            , CASE WHEN veiculo_uniorcam.num_orgao IS NOT NULL AND veiculo_uniorcam.num_unidade IS NOT NULL
                                   THEN veiculo_uniorcam.exercicio
                                   ELSE despesa.exercicio
                               END AS exercicio_locacao
                            , veiculo_propriedade.cod_veiculo
                            , MAX(veiculo_propriedade.timestamp) AS timestamp
                            , veiculo_locacao.cod_entidade
                            , veiculo_locacao.dt_inicio
                            , veiculo_locacao.dt_termino
                            , '02'::VARCHAR AS situacao_veiculo
                         FROM frota.veiculo
                   INNER JOIN frota.veiculo_propriedade
                           ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                          AND veiculo_propriedade.proprio = false
                   INNER JOIN frota.terceiros
                           ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                          AND terceiros.timestamp = veiculo_propriedade.timestamp
                   INNER JOIN frota.veiculo_locacao
                           ON veiculo_locacao.cod_veiculo = veiculo.cod_veiculo
                    LEFT JOIN patrimonio.veiculo_uniorcam
                           ON veiculo.cod_veiculo = veiculo_uniorcam.cod_veiculo
                    LEFT JOIN empenho.empenho
                           ON empenho.exercicio    = veiculo_locacao.exercicio
                          AND empenho.cod_entidade = veiculo_locacao.cod_entidade
                          AND empenho.cod_empenho  = veiculo_locacao.cod_empenho
                    LEFT JOIN empenho.pre_empenho
                           ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                          AND pre_empenho.exercicio       = empenho.exercicio
                    LEFT JOIN empenho.pre_empenho_despesa
                           ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                          AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                    LEFT JOIN orcamento.despesa
                           ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                          AND despesa.exercicio  = pre_empenho_despesa.exercicio
                     GROUP BY cod_unidade
                            , cod_orgao
                            , veiculo_propriedade.cod_veiculo
                            , veiculo_locacao.cod_entidade
                            , veiculo_uniorcam.cod_veiculo
                            , veiculo_locacao.dt_inicio
                            , veiculo_locacao.dt_termino
                            , exercicio_locacao
                             ) AS descricao_veiculo_locado
                    ON  descricao_veiculo_locado.cod_veiculo = veiculo_propriedade.cod_veiculo
                   AND  descricao_veiculo_locado.timestamp = veiculo_propriedade.timestamp

           LEFT JOIN ( SELECT veiculo_uniorcam.num_orgao AS cod_orgao
                            , veiculo_uniorcam.num_unidade AS cod_unidade
                            , veiculo_uniorcam.exercicio AS exercicio_cessao
                            , veiculo_propriedade.cod_veiculo
                            , MAX(veiculo_propriedade.timestamp) AS timestamp
                            , veiculo_cessao.dt_inicio
                            , veiculo_cessao.dt_termino
                            , '03'::VARCHAR AS situacao_veiculo
                         FROM frota.veiculo
                   INNER JOIN frota.veiculo_propriedade
                           ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                          AND veiculo_propriedade.proprio = false
                   INNER JOIN frota.terceiros
                           ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                          AND terceiros.timestamp = veiculo_propriedade.timestamp
                   INNER JOIN frota.veiculo_cessao
                           ON veiculo_cessao.cod_veiculo = veiculo.cod_veiculo
                    LEFT JOIN patrimonio.veiculo_uniorcam
                           ON veiculo.cod_veiculo = veiculo_uniorcam.cod_veiculo
                     GROUP BY cod_unidade
                            , cod_orgao
                            , veiculo_propriedade.cod_veiculo
                            , veiculo_uniorcam.cod_veiculo
                            , veiculo_cessao.dt_inicio
                            , veiculo_cessao.dt_termino
                            , exercicio_cessao
                    ) AS descricao_veiculo_cessao
                 ON descricao_veiculo_cessao.cod_veiculo = veiculo_propriedade.cod_veiculo
                AND descricao_veiculo_cessao.timestamp = veiculo_propriedade.timestamp

         INNER JOIN administracao.configuracao_entidade
                 ON configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
                AND configuracao_entidade.cod_modulo   = 55
                AND configuracao_entidade.exercicio    = '".Sessao::getExercicio()."'
                AND configuracao_entidade.cod_entidade = ".$this->getDado('entidades')."

               WHERE transporte_escolar.mes = TO_CHAR(TO_DATE('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy'),'mm')::INTEGER
                 AND transporte_escolar.exercicio = '".Sessao::getExercicio()."'
                 
            GROUP BY tipo_registro
                   , configuracao_entidade.valor
                   , cod_unidade_sub
                   , transporte_escolar.cod_veiculo
                   , nome_estabelecimento
                   , localidade
                   , qtde_dias_rodados
                   , distacia_estabelecimento
                   , numero_passageiros
                   , turnos

                ";

        return $stSql;
    }

    
    function recuperaVeiculosBaixados(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaVeiculosBaixados",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    function montaRecuperaVeiculosBaixados()
    {
        $stSql = "
              SELECT 40 AS tipo_registro
                   , LPAD(configuracao_entidade.valor,2,'0') AS uniorcam_cod_orgao
                   , CASE WHEN (descricao_veiculo.num_orgao IS NOT NULL AND descricao_veiculo.num_unidade IS NOT NULL)
                          THEN LPAD(LPAD(descricao_veiculo.num_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo.num_unidade::VARCHAR, 2, '0'), 5, '0')
                          WHEN descricao_veiculo_locado.cod_orgao IS NOT NULL AND descricao_veiculo_locado.cod_unidade IS NOT NULL
                          THEN LPAD(LPAD(descricao_veiculo_locado.cod_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo_locado.cod_unidade::VARCHAR, 2, '0'), 5, '0')
                          WHEN descricao_veiculo_cessao.cod_orgao IS NOT NULL AND descricao_veiculo_cessao.cod_unidade IS NOT NULL
                          THEN LPAD(LPAD(descricao_veiculo_cessao.cod_orgao::VARCHAR, 2, '0')||LPAD(descricao_veiculo_cessao.cod_unidade::VARCHAR, 2, '0'), 5, '0')
                          ELSE LPAD('0', 5, '0')
                      END AS cod_unidade_sub
                   , veiculo_baixado.cod_veiculo
                   , veiculo_baixado.cod_tipo_baixa AS cod_tipo
                   , CASE WHEN (veiculo_baixado.cod_tipo_baixa = 99)
                          THEN TRIM(veiculo_baixado.motivo)
                          ELSE ''
                      END AS descbaixa
                   , TO_CHAR(veiculo_baixado.dt_baixa,'ddmmyyyy') AS dt_baixa
                FROM frota.veiculo_baixado
          INNER JOIN frota.veiculo
                  ON veiculo.cod_veiculo = veiculo_baixado.cod_veiculo
          INNER JOIN frota.veiculo_propriedade
                  ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                 AND veiculo_propriedade.timestamp = ( SELECT MAX(vp.timestamp)
                                                         FROM frota.veiculo_propriedade as vp
                                                        WHERE vp.cod_veiculo = veiculo_propriedade.cod_veiculo )

           LEFT JOIN ( SELECT bem.descricao
                            , bem.cod_bem
                            , bem_comprado.num_orgao
                            , bem_comprado.num_unidade
                            , bem_comprado.exercicio
                            , veiculo_propriedade.cod_veiculo
                            , MAX(veiculo_propriedade.timestamp) AS timestamp
                            , bem_comprado.cod_entidade
                            , bem.dt_aquisicao
                            , '01'::VARCHAR AS situacao_veiculo
                         FROM frota.veiculo
                   INNER JOIN frota.veiculo_propriedade
                           ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                          AND veiculo_propriedade.proprio = true
                   INNER JOIN frota.proprio
                           ON proprio.cod_veiculo = veiculo_propriedade.cod_veiculo
                          AND proprio.timestamp = veiculo_propriedade.timestamp
                   INNER JOIN patrimonio.bem
                           ON bem.cod_bem = proprio.cod_bem
                   INNER JOIN patrimonio.bem_comprado
                           ON bem_comprado.cod_bem = bem.cod_bem
                     GROUP BY bem.descricao
                            , veiculo_propriedade.cod_veiculo
                            , bem.cod_bem
                            , bem_comprado.cod_entidade
                            , bem_comprado.num_orgao
                            , bem_comprado.num_unidade
                            , bem_comprado.exercicio
                            , veiculo.dt_aquisicao
                             ) AS descricao_veiculo
                 ON  descricao_veiculo.cod_veiculo = veiculo_propriedade.cod_veiculo
                AND descricao_veiculo.timestamp = veiculo_propriedade.timestamp
                
          LEFT JOIN ( SELECT CASE WHEN veiculo_uniorcam.num_orgao IS NOT NULL AND veiculo_uniorcam.num_unidade IS NOT NULL
                                   THEN veiculo_uniorcam.num_orgao
                                   ELSE despesa.num_orgao
                               END AS cod_orgao
                            , CASE WHEN veiculo_uniorcam.num_orgao IS NOT NULL AND veiculo_uniorcam.num_unidade IS NOT NULL
                                   THEN veiculo_uniorcam.num_unidade
                                   ELSE despesa.num_unidade
                               END AS cod_unidade
                            , CASE WHEN veiculo_uniorcam.num_orgao IS NOT NULL AND veiculo_uniorcam.num_unidade IS NOT NULL
                                   THEN veiculo_uniorcam.exercicio
                                   ELSE despesa.exercicio
                               END AS exercicio_locacao
                            , veiculo_propriedade.cod_veiculo
                            , MAX(veiculo_propriedade.timestamp) AS timestamp
                            , veiculo_locacao.cod_entidade
                            , veiculo_locacao.dt_inicio
                            , veiculo_locacao.dt_termino
                            , '02'::VARCHAR AS situacao_veiculo
                         FROM frota.veiculo
                   INNER JOIN frota.veiculo_propriedade
                           ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                          AND veiculo_propriedade.proprio = false
                   INNER JOIN frota.terceiros
                           ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                          AND terceiros.timestamp = veiculo_propriedade.timestamp
                   INNER JOIN frota.veiculo_locacao
                           ON veiculo_locacao.cod_veiculo = veiculo.cod_veiculo
                    LEFT JOIN patrimonio.veiculo_uniorcam
                           ON veiculo.cod_veiculo = veiculo_uniorcam.cod_veiculo
                    LEFT JOIN empenho.empenho
                           ON empenho.exercicio    = veiculo_locacao.exercicio
                          AND empenho.cod_entidade = veiculo_locacao.cod_entidade
                          AND empenho.cod_empenho  = veiculo_locacao.cod_empenho
                    LEFT JOIN empenho.pre_empenho
                           ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                          AND pre_empenho.exercicio       = empenho.exercicio
                    LEFT JOIN empenho.pre_empenho_despesa
                           ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                          AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio
                    LEFT JOIN orcamento.despesa
                           ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa
                          AND despesa.exercicio  = pre_empenho_despesa.exercicio
                     GROUP BY cod_unidade
                            , cod_orgao
                            , veiculo_propriedade.cod_veiculo
                            , veiculo_locacao.cod_entidade
                            , veiculo_uniorcam.cod_veiculo
                            , veiculo_locacao.dt_inicio
                            , veiculo_locacao.dt_termino
                            , exercicio_locacao
                             ) AS descricao_veiculo_locado
                    ON  descricao_veiculo_locado.cod_veiculo = veiculo_propriedade.cod_veiculo
                   AND  descricao_veiculo_locado.timestamp = veiculo_propriedade.timestamp

           LEFT JOIN ( SELECT veiculo_uniorcam.num_orgao AS cod_orgao
                            , veiculo_uniorcam.num_unidade AS cod_unidade
                            , veiculo_uniorcam.exercicio AS exercicio_cessao
                            , veiculo_propriedade.cod_veiculo
                            , MAX(veiculo_propriedade.timestamp) AS timestamp
                            , veiculo_cessao.dt_inicio
                            , veiculo_cessao.dt_termino
                            , '03'::VARCHAR AS situacao_veiculo
                         FROM frota.veiculo
                   INNER JOIN frota.veiculo_propriedade
                           ON veiculo_propriedade.cod_veiculo = veiculo.cod_veiculo
                          AND veiculo_propriedade.proprio = false
                   INNER JOIN frota.terceiros
                           ON terceiros.cod_veiculo = veiculo_propriedade.cod_veiculo
                          AND terceiros.timestamp = veiculo_propriedade.timestamp
                   INNER JOIN frota.veiculo_cessao
                           ON veiculo_cessao.cod_veiculo = veiculo.cod_veiculo
                    LEFT JOIN patrimonio.veiculo_uniorcam
                           ON veiculo.cod_veiculo = veiculo_uniorcam.cod_veiculo
                     GROUP BY cod_unidade
                            , cod_orgao
                            , veiculo_propriedade.cod_veiculo
                            , veiculo_uniorcam.cod_veiculo
                            , veiculo_cessao.dt_inicio
                            , veiculo_cessao.dt_termino
                            , exercicio_cessao
                    ) AS descricao_veiculo_cessao
                 ON descricao_veiculo_cessao.cod_veiculo = veiculo_propriedade.cod_veiculo
                AND descricao_veiculo_cessao.timestamp = veiculo_propriedade.timestamp

         INNER JOIN administracao.configuracao_entidade
                 ON configuracao_entidade.parametro    = 'tcemg_codigo_orgao_entidade_sicom'
                AND configuracao_entidade.cod_modulo   = 55
                AND configuracao_entidade.exercicio    = '".Sessao::getExercicio()."'
                AND configuracao_entidade.cod_entidade = ".$this->getDado('entidades')."

              WHERE veiculo_baixado.dt_baixa BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                 AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
           GROUP BY tipo_registro
                  , uniorcam_cod_orgao
                  , cod_unidade_sub
                  , veiculo_baixado.cod_veiculo
                  , cod_tipo
                  , descbaixa
                  , dt_baixa
        ";
        return $stSql;
    }
    
    public function __destruct(){}

}

?>