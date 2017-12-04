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
  * Classe de mapeamento da tabela ECONOMICO.LICENCA_DIAS_SEMANA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMLicencaDiasSemana.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.8  2007/04/23 19:00:43  dibueno
retirado espaço vazio ao final do arquivo

Revision 1.7  2007/03/02 14:52:19  dibueno
Bug #7676#

Revision 1.6  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.LICENCA_DIAS_SEMANA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMLicencaDiasSemana extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMLicencaDiasSemana()
{
    parent::Persistente();
    $this->setTabela('economico.licenca_dias_semana');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_licenca,exercicio,cod_dia');

    $this->AddCampo('cod_licenca','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('cod_dia','integer',true,'',true,true);
    $this->AddCampo('hr_inicio','time',true,'',false,false);
    $this->AddCampo('hr_termino','time',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                           \n";
    $stSql .= "     lds.*,                                                      \n";
    $stSql .= "     ads.nom_dia,                                                \n";
    $stSql .= "     substring( ads.nom_dia from 1 for 3) as abreviatura_dia     \n";
    $stSql .= "FROM                                                             \n";
    $stSql .= "    economico.licenca_dias_semana lds,                           \n";
    $stSql .= "    administracao.dias_semana ads                                \n";
    $stSql .= "WHERE                                                            \n";
    $stSql .= "    lds.cod_dia = ads.cod_dia                                    \n";

    return $stSql;

}

}
