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
    * Classe de mapeamento para administracao.configuracao_entidade
    * Data de Criação: 22/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    $Revision: 23202 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-06-12 17:47:59 -0300 (Ter, 12 Jun 2007) $

    Casos de uso: uc-01.01.00
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TAdministracaoConfiguracaoEntidade extends Persistente
{
function TAdministracaoConfiguracaoEntidade()
{
    parent::Persistente();
    $this->setTabela('administracao.configuracao_entidade');
    $this->setComplementoChave('exercicio,cod_entidade,cod_modulo,parametro');

    $this->AddCampo('exercicio',   'char',    false, 4, false, false);
    $this->AddCampo('cod_entidade','integer', true, '', false, false);
    $this->AddCampo('cod_modulo',  'integer', true, '', false, false);
    $this->AddCampo('parametro',   'varchar', true, 40, false, false);
    $this->AddCampo('valor',       'text',    true, '', false, false);
}

function recuperaExportacaoOrgao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaExportacaoOrgao();
    $this->setDebug( $stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaExportacaoOrgao()
{
    $stSql = "
    SELECT codigo_unidade_gestora.cod_orgao
         , tipo_unidade_gestora.tipo_orgao
         , responsavel_unidade_gestora.cpf
      FROM ( SELECT valor AS cod_orgao
                  , cod_entidade
               FROM administracao.configuracao_entidade
              WHERE parametro = 'tcemg_codigo_orgao_entidade_sicom'
                AND exercicio = '".Sessao::getExercicio()."'
         ) AS codigo_unidade_gestora

 LEFT JOIN ( SELECT valor AS tipo_orgao
                  , cod_entidade
               FROM administracao.configuracao_entidade
              WHERE parametro = 'tcemg_tipo_orgao_entidade_sicom'
                AND exercicio = '".Sessao::getExercicio()."'
         ) AS tipo_unidade_gestora
        ON tipo_unidade_gestora.cod_entidade = codigo_unidade_gestora.cod_entidade

 LEFT JOIN ( SELECT sw_cgm_pessoa_fisica.cpf
                  , configuracao_entidade.cod_entidade
               FROM administracao.configuracao_entidade
               JOIN sw_cgm_pessoa_fisica
                 ON sw_cgm_pessoa_fisica.numcgm = configuracao_entidade.valor::INTEGER
              WHERE parametro = 'tcemg_cgm_responsavel'
                AND exercicio = '".Sessao::getExercicio()."'
         ) AS responsavel_unidade_gestora
        ON responsavel_unidade_gestora.cod_entidade = codigo_unidade_gestora.cod_entidade

     WHERE codigo_unidade_gestora.cod_entidade IN (".$this->getDado('entidades').")
    ";

    return $stSql;
}

}
