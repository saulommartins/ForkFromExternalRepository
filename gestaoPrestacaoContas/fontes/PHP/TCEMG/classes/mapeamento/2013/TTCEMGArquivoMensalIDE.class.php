<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Solu›es em Gest‹o Pœblica                                *
    * @copyright (c) 2013 Confedera‹o Nacional de Munic’pos                         *
    * @author Confedera‹o Nacional de Munic’pios                                    *
    *                                                                                *
    * Este programa Ž software livre; voc pode redistribu’-lo e/ou modific‡-lo  sob *
    * os termos da Licena Pœblica Geral GNU conforme publicada pela  Free  Software *
    * Foundation; tanto a vers‹o 2 da Licena, como (a seu critŽrio) qualquer vers‹o *
    *                                                                                *
    * Este  programa  Ž  distribu’do  na  expectativa  de  que  seja  œtil,   porŽm, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia impl’cita  de  COMERCIABILIDADE  OU *
    * ADEQUA‚ÌO A UMA FINALIDADE ESPECêFICA. Consulte a Licena Pœblica Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Voc deve ter recebido uma c—pia da Licena Pœblica Geral  do  GNU  junto  com *
    * este programa; se n‹o, escreva para  a  Free  Software  Foundation,  Inc.,  no *
    * endereo 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.               *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Classe de mapeamento do arquivo CVC.inc.php
    * Data de Cria‹o:  27/01/2014

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