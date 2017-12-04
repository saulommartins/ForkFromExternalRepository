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
  * Classe de mapeamento da tabela CALENDARIO_FERIADO_VARIAVEL
  * Data de Criação: 04/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento

  Caso de uso: uc-04.02.02
               uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  CALENDARIO_FERIADO_VARIAVEL
  * Data de Criação: 04/08/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Eduardo Martins

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCalendarioFeriadoVariavel extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCalendarioFeriadoVariavel()
{
    parent::Persistente();
    $this->setTabela('calendario.feriado_variavel');

    $this->setCampoCod('cod_feriado');
    $this->setComplementoChave('');

    $this->AddCampo('cod_feriado','integer',true,'',true,true);

}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                    \n";
    $stSQL .= "     F.cod_feriado,                                        \n";
    $stSQL .= "     to_char (F.dt_feriado, 'dd/mm/yyyy') as dt_feriado,   \n";
    $stSQL .= "     F.descricao,                                          \n";
    $stSQL .= "  CASE f.abrangencia                                       \n";
    $stSQL .= "    WHEN 'E' THEN 'Estadual'                               \n";
    $stSQL .= "    WHEN 'F' THEN 'Federal'                                \n";
    $stSQL .= "    WHEN 'M' THEN 'Municipal'                              \n";
    $stSQL .= "  END as abrangencia,                                      \n";
    $stSQL .= "  CASE tipoferiado                                         \n";
    $stSQL .= "    WHEN 'F' THEN 'Fixo'                                   \n";
    $stSQL .= "    WHEN 'V' THEN 'Variável'                               \n";
    $stSQL .= "  END as tipoferiado                                       \n";
    $stSQL .= " FROM                                                      \n";
    $stSQL .= "   calendario.feriado AS F,                            \n";
    $stSQL .= "   calendario.feriado_variavel AS FV                   \n";
    $stSQL .= " WHERE                                                     \n";
    $stSQL .= "     F.cod_feriado = FV.cod_feriado                        \n";

    return $stSQL;
}

}
