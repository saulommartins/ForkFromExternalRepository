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
  * Classe de mapeamento da tabela CALENDARIO_FERIADO
  * Data de Criação: 04/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-11 14:58:23 -0300 (Seg, 11 Jun 2007) $

    Caso de uso: uc-04.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CALENDARIO_FERIADO
  * Data de Criação: 04/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCalendarioFeriado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCalendarioFeriado()
{
    parent::Persistente();
    $this->setTabela('calendario.feriado');

    $this->setCampoCod('cod_feriado');
    $this->setComplementoChave('');

    $this->AddCampo('cod_feriado','integer',true,''   ,true,false);
    $this->AddCampo('dt_feriado' ,'date'   ,true,''   ,false,false);
    $this->AddCampo('descricao'  ,'varchar',true,'100',false,false);
    $this->AddCampo('tipoferiado','varchar',true,'1'  ,false,false);
    $this->AddCampo('abrangencia','varchar',true,'1'  ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                                     \n";
    $stSql .= "  f.cod_feriado,                                                           \n";
    $stSql .= "  to_char(dt_feriado, 'dd/mm/yyyy') as dt_feriado,                         \n";
    $stSql .= "  descricao,                                                               \n";
    $stSql .= "  CASE abrangencia                                                         \n";
    $stSql .= "    WHEN 'E' THEN 'Estadual'                                               \n";
    $stSql .= "    WHEN 'F' THEN 'Federal'                                                \n";
    $stSql .= "    WHEN 'M' THEN 'Municipal'                                              \n";
    $stSql .= "  END as abrangencia,                                                      \n";
    $stSql .= "  CASE tipoferiado                                                         \n";
    $stSql .= "    WHEN 'D' THEN 'Dia compensado'                                         \n";
    $stSql .= "    WHEN 'F' THEN 'Fixo'                                                   \n";
    $stSql .= "    WHEN 'P' THEN 'Ponto facultativo'                                      \n";
    $stSql .= "    WHEN 'V' THEN 'Variável'                                               \n";
    $stSql .= "  END as tipoferiado,                                                      \n";
    $stSql .= "  tipo_feriado(to_char(dt_feriado,'dd/mm/yyyy'),'".Sessao::getEntidade()."') as tipo_cor    \n";
    $stSql .= "FROM                                                                       \n";
    $stSql .= "  calendario.feriado  as f                                          \n";
    $stSql .= "WHERE                                                                      \n";
    if ($this->getDado("ano")) {
       $stSql .= "  (to_char(f.dt_feriado, 'yyyy') = '" . $this->getDado("ano") . "')     \n";
    }

    return $stSql;
}

}
