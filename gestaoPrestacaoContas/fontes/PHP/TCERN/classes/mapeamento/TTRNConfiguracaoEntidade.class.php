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
    * Data de Criação: 30/08/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracaoEntidade.class.php');

/**
  *
  * Data de Criação: 30/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTRNConfiguracaoEntidade extends TAdministracaoConfiguracaoEntidade
{
/**
    * Método Construtor
    * @access Private
*/
function TTRNConfiguracaoEntidade()
{
    parent::TAdministracaoConfiguracaoEntidade();
    $this->setDado('exercicio'  ,Sessao::getExercicio());
    $this->setDado('cod_modulo' ,49);
    $this->setDado('parametro'  ,'cod_orgao_tce');
}

function montaRecuperaRelacionamento()
{
    $stSql = " SELECT  conf.*,ent.*,cgm.nom_cgm                                                        \n";
    $stSql .= " FROM    orcamento.entidade as ent                                                       \n";
    $stSql .= "         LEFT JOIN administracao.configuracao_entidade as conf                           \n";
    $stSql .= "         ON ( ent.exercicio = conf.exercicio AND ent.cod_entidade = conf.cod_entidade    \n";
    $stSql .= "         AND conf.cod_modulo = ".$this->getDado('cod_modulo')."                          \n";
    $stSql .= "         AND conf.parametro = '".$this->getDado('parametro')."' )                        \n";
    $stSql .= "        ,sw_cgm  as cgm                                                                  \n";
    $stSql .= " WHERE ent.numcgm = cgm.numcgm                                                           \n";
    $stSql .= " AND   ent.exercicio = '".$this->getDado('exercicio')."'                                 \n";
    $stSql .= " AND   ent.cod_entidade = ( SELECT valor::INTEGER
                                                 FROM administracao.configuracao
                                                WHERE parametro = 'cod_entidade_prefeitura'
                                                  AND exercicio = '".$this->getDado('exercicio')."' )   \n";
    $stSql .= " ORDER BY ent.exercicio, ent.cod_entidade                                                \n";

    return $stSql;
}

}
