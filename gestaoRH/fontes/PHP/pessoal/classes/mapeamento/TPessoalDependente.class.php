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
  * Classe de mapeamento da tabela PESSOAL.DEPENDENTE
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida

  * @package URBEM
  * @subpackage Mapeamento

  $Revision: 30566 $
  $Name$
  $Author: souzadl $
  $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

  Caso de uso: uc-04.04.07

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.DEPENDENTE
  * Data de Criação: 14/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Rafael Almeida
  *                        Vandre Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalDependente extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalDependente()
{
    parent::Persistente();
    $this->setTabela('pessoal.dependente');

    $this->setCampoCod('cod_dependente');
    $this->setComplementoChave('');

    $this->AddCampo('cod_dependente',           'integer'   ,true,''  ,true ,false);
    $this->AddCampo('numcgm',                   'integer'   ,true,''  ,false,true);
    $this->AddCampo('dependente_sal_familia',   'boolean'   ,true,''  ,false,false);
    $this->AddCampo('dependente_invalido',      'boolean'   ,true,''  ,false,false);
    $this->AddCampo('carteira_vacinacao',       'boolean'   ,true,''  ,false,false);
    $this->AddCampo('comprovante_matricula',    'boolean'   ,true,''  ,false,false);
    $this->AddCampo('dependente_prev',    		'boolean'   ,true,''  ,false,false);
    $this->AddCampo('cod_grau',                 'integer'   ,true,''  ,false,false);
    $this->AddCampo('cod_vinculo',              'integer'   ,true,''  ,false,true);
    $this->AddCampo('dt_inicio_sal_familia',    'date'      ,false,'' ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql = " SELECT                                                                                       \n";
    $stSql.= "    PD.*,                                                                                     \n";
    $stSql.= "    PS.cod_servidor,                                                                          \n";
    $stSql.= "    to_char(PD.dt_inicio_sal_familia,'dd/mm/yyyy')       as dt_inicio_sal_familia,            \n";
    $stSql.= "    PDC.cod_cid,                                                                              \n";
    $stSql.= "    sw_cgm.nom_cgm,                                                                           \n";
    $stSql.= "    CASE WHEN sw_cgm_pessoa_fisica.sexo = 'f'                                                 \n";
    $stSql.= "         THEN 'Feminino'                                                                      \n";
    $stSql.= "         ELSE 'Masculino'                                                                     \n";
    $stSql.= "          END as sexo,                                                                        \n";
    $stSql.= "    to_char(sw_cgm_pessoa_fisica.dt_nascimento,'dd/mm/yyyy') as dt_nascimento                 \n";
    $stSql.= "    , vinculo_irrf.descricao as descricao_vinculo                                             \n";
    $stSql.= "    , cid.descricao as descricao_cid                                                          \n";
    $stSql.= "    , sw_escolaridade.descricao as escolaridade                                               \n";
    $stSql.= "FROM                                                                                          \n";
    $stSql.= "   pessoal.servidor             PS,                                                           \n";
    $stSql.= "   pessoal.servidor_dependente PSD,                                                           \n";
    $stSql.= "   folhapagamento.vinculo_irrf,                                                               \n";
    $stSql.= "   ".$this->getTabela ()."          PD                                                        \n";
    $stSql.= "   left join pessoal.dependente_cid as  PDC                                                   \n";
    $stSql.= "   on(PD.cod_dependente  = PDC.cod_dependente)                                                \n";
    $stSql.= "   left join pessoal.cid                                                                      \n";
    $stSql.= "   on(PDC.cod_cid  = cid.cod_cid)                                                             \n";
    $stSql.= "LEFT JOIN sw_cgm                                                                              \n";
    $stSql.= "       ON PD.numcgm = sw_cgm.numcgm                                                           \n";
    $stSql.= "LEFT JOIN sw_cgm_pessoa_fisica                                                                \n";
    $stSql.= "       ON PD.numcgm = sw_cgm_pessoa_fisica.numcgm                                             \n";
    $stSql.= "LEFT JOIN sw_escolaridade                                                                     \n";
    $stSql.= "       ON sw_escolaridade.cod_escolaridade = sw_cgm_pessoa_fisica.cod_escolaridade            \n";
    $stSql.= "WHERE                                                                                         \n";
    $stSql.= "   PS.cod_servidor    = PSD.cod_servidor         and                                          \n";
    $stSql.= "   PSD.cod_dependente = PD.cod_dependente                                                     \n";
    $stSql.= "   and PD.cod_vinculo = vinculo_irrf.cod_vinculo                                              \n";
    $stSql.= "   AND PSD.cod_dependente::varchar||PSD.cod_servidor::varchar NOT IN (                        \n";
    $stSql.= "   SELECT cod_dependente::varchar||cod_servidor::varchar FROM pessoal.dependente_excluido )   \n";

    return $stSql;

}

}
