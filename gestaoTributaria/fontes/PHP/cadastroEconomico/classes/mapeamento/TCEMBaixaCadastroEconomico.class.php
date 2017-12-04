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
  * Classe de mapeamento da tabela ECONOMICO.BAIXA_CADASTRO_ECONOMICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMBaixaCadastroEconomico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.9  2007/02/09 18:33:15  rodrigo
#8342#

Revision 1.8  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.BAIXA_CADASTRO_ECONOMICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMBaixaCadastroEconomico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMBaixaCadastroEconomico()
{
    parent::Persistente();
    $this->setTabela('economico.baixa_cadastro_economico');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_economica,dt_incicio,timestamp');

    $this->AddCampo('inscricao_economica','integer'  ,true ,'',true ,true);
    $this->AddCampo('dt_inicio'          ,'date'     ,true ,'',true ,false);
    $this->AddCampo('dt_termino'         ,'date'     ,false,'',false,false);
    $this->AddCampo('de_oficio'          ,'boolean'  ,true ,'',false,false);
    $this->AddCampo('motivo'             ,'text'     ,true ,'',false,false);
    $this->AddCampo('timestamp'          ,'timestamp',false ,'',true,false);
    $this->AddCampo('cod_tipo'           ,'integer'  ,true ,'',false,true);
}
}
