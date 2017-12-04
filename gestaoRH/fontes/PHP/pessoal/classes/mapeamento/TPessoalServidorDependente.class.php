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
  * Classe de mapeamento da tabela PESSOAL.SERVIDOR_DEPENDENTE
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.SERVIDOR_DEPENDENTE
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalServidorDependente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalServidorDependente()
{
    parent::Persistente();
    $this->setTabela('pessoal.servidor_dependente');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_servidor,cod_dependente','timestamp');

    $this->AddCampo('cod_servidor'  ,'integer'  ,true   ,'',true    ,true );
    $this->AddCampo('cod_dependente','integer'  ,true   ,'',true    ,true );
    $this->AddCampo('dt_inicio'     ,'date'     ,false  ,'',false   ,false);
    $this->AddCampo('timestamp'     ,'timestamp',false  ,'',true    ,false);

}

function recuperaQuantDependentesServidor(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaRecuperaQuantDependentesServidor().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaQuantDependentesServidor()
{
    $stSql .= "SELECT count(servidor.cod_servidor) as contador                                      \n";
    $stSql .= " FROM pessoal.servidor                                                               \n";
    $stSql .= "    , pessoal.servidor_contrato_servidor                                             \n";
    $stSql .= "    , pessoal.servidor_dependente                                                    \n";
    $stSql .= "WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                \n";
    $stSql .= "  AND servidor.cod_servidor                   = servidor_dependente.cod_servidor     \n";

    return $stSql;
}

function recuperaQuantDependentesIRRFServidor(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaQuantDependentesIRRFServidor",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaQuantDependentesIRRFServidor()
{
    $stSql .= "select COALESCE( COUNT(servidor_dependente.cod_servidor),0 ) as contador\n";
    $stSql .= "  from pessoal.servidor_dependente\n";
    $stSql .= "  left outer join pessoal.dependente\n";
    $stSql .= "    on dependente.cod_dependente = servidor_dependente.cod_dependente\n";
    $stSql .= "  left outer join public.sw_cgm_pessoa_fisica\n";
    $stSql .= "    on dependente.numcgm = sw_cgm_pessoa_fisica.numcgm\n";
    $stSql .= "  left outer join folhapagamento.vinculo_irrf\n";
    $stSql .= "    on vinculo_irrf.cod_vinculo = dependente.cod_vinculo\n";
    $stSql .= "  left outer join pessoal.dependente_excluido\n";
    $stSql .= "    on servidor_dependente.cod_dependente = dependente_excluido.cod_dependente\n";
    $stSql .= "   and servidor_dependente.cod_servidor = dependente_excluido.cod_servidor\n";
    $stSql .= " where sw_cgm_pessoa_fisica.dt_nascimento is not null\n";
    $stSql .= "   and dependente.cod_vinculo > 0\n";
    $stSql .= "   and ( vinculo_irrf.idade_limite = 0 \n";
    $stSql .= "         or (idade( to_char(sw_cgm_pessoa_fisica.dt_nascimento,'yyyy-mm-dd' ), to_date('".$this->getDado("dt_final")."','dd/mm/yyyy')::varchar)) <= vinculo_irrf.idade_limite )\n";
    $stSql .= "   and dependente_excluido.cod_servidor is null   \n";

    return $stSql;
}

function recuperaDependentesDeServidor(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaDependentesDeServidor",$rsRecordSet,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaDependentesDeServidor()
{
    $stSql .= "SELECT servidor_dependente.*                                                             \n";
    $stSql .= "     , dependente.numcgm                                                                 \n";
    $stSql .= "     , servidor.numcgm as numcgm_servidor                                                \n";
    $stSql .= "     , contrato.registro                                                                 \n";
    $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm_servidor   \n";
    $stSql .= "     , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = dependente.numcgm) as nom_cgm          \n";
    $stSql .= "  FROM pessoal.servidor_dependente                             \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                      \n";
    $stSql .= "     , pessoal.contrato                                        \n";
    $stSql .= "     , pessoal.dependente                                      \n";
    $stSql .= "     , pessoal.servidor                                        \n";
    $stSql .= " WHERE servidor_dependente.cod_servidor = servidor_contrato_servidor.cod_servidor        \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_contrato = contrato.cod_contrato                   \n";
    $stSql .= "   AND servidor_dependente.cod_dependente = dependente.cod_dependente                    \n";
    $stSql .= "   AND servidor_dependente.cod_servidor = servidor.cod_servidor                          \n";
    $stSql .= "   AND NOT EXISTS (SELECT 1                                                              \n";
    $stSql .= "                     FROM pessoal.dependente_excluido          \n";
    $stSql .= "                    WHERE dependente_excluido.cod_dependente = servidor_dependente.cod_dependente\n";
    $stSql .= "                      AND dependente_excluido.cod_servidor = servidor_dependente.cod_servidor)\n";

    return $stSql;
}

}
