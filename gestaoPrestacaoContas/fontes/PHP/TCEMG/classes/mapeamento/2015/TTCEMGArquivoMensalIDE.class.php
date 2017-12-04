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
    * Classe de mapeamento do arquivo TTCEMGArquivoMensalIDE.class.php
    * Data de Criação :  27/01/2014

    * @author Analista: Sergio
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCEMGArquivoMensalIDE.class.php 65300 2016-05-10 20:27:55Z evandro $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGArquivoMensalIDE extends Persistente
{
    public function TTCEMGArquivoMensalIDE()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaDadosExportacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDadosExportacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDadosExportacao()
    {
        $stSql = "
        SELECT ( SELECT cnpj 
                   FROM sw_cgm_pessoa_juridica
              LEFT JOIN administracao.configuracao 
                     ON parametro = 'cod_entidade_prefeitura' 
                    AND configuracao.exercicio = '".Sessao::getExercicio()."'
              LEFT JOIN orcamento.entidade 
                     ON cod_entidade=configuracao.valor::integer 
                    AND entidade.exercicio = configuracao.exercicio
                  WHERE sw_cgm_pessoa_juridica.numcgm=entidade.numcgm
             ) AS cnpj_municipio
             , ( SELECT DISTINCT cod_municipio
                   FROM tcemg.configurar_ide
             ) AS cod_municipio
             , LPAD((SELECT valor
                       FROM tcemg.orgao
                 INNER JOIN administracao.configuracao_entidade
                         ON configuracao_entidade.valor::integer = orgao.num_orgao
                      WHERE  configuracao_entidade.exercicio   = ACE.exercicio 
                        AND configuracao_entidade.cod_entidade = ACE.cod_entidade 
                        AND parametro = 'tcemg_codigo_orgao_entidade_sicom'
             ), 3, '0') AS cod_orgao
             , ( SELECT valor 
                   FROM tcemg.orgao
             INNER JOIN administracao.configuracao_entidade
                     ON configuracao_entidade.valor::integer = orgao.num_orgao
                  WHERE configuracao_entidade.exercicio    = ACE.exercicio 
                    AND configuracao_entidade.cod_entidade = ACE.cod_entidade 
                    AND parametro = 'tcemg_tipo_orgao_entidade_sicom'
             ) AS tipo_orgao
             , '".Sessao::getExercicio()."' AS exercicio_referencia
             , '".$this->getDado('mes')."' AS mes_referencia
             , to_char(CURRENT_DATE::timestamp,'ddmmyyyy') as data_geracao
             , to_char(CURRENT_DATE::timestamp,'yyyymmdd')||''||'".Sessao::getExercicio()."'||'".$this->getDado('mes')."' as cod_remessa
          FROM administracao.configuracao_entidade AS ACE
         WHERE ACE.exercicio = '".Sessao::getExercicio()."' 
           AND ACE.cod_entidade IN (".$this->getDado('entidades').") 
           AND ACE.parametro = 'tcemg_codigo_orgao_entidade_sicom' ";
        return $stSql;
    }
    
    public function __destruct(){}

}

?>