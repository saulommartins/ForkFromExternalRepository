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
  * Classe de mapeamento da tabela PESSOAL.ASSENTAMENTO_EVENTO
  * Data de Criação: 02/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ASSENTAMENTO_EVENTO
  * Data de Criação: 02/02/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoEvento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoEvento()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento_evento');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento,cod_evento,timestamp');

    $this->AddCampo('cod_assentamento','integer',true,'',true,true);
    $this->AddCampo('cod_evento','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,true);
    $this->AddCampo('vigencia','date',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSQL .= " SELECT                                                 \n";
    $stSQL .= "     PE.*                                               \n";
    $stSQL .= " FROM                                                   \n";
    $stSQL .= "   (select                                              \n";
    $stSQL .= "         cod_assentamento, max(timestamp) as timestamp  \n";
    $stSQL .= "     from                                               \n";
    $stSQL .= "         pessoal.assentamento                       \n";
    $stSQL .= "     group by cod_assentamento) as pa,                  \n";
    $stSQL .= "    pessoal.assentamento_evento as PAE,             \n";
    $stSQL .= "    folhapagamento.evento               as PE              \n";
    $stSQL .= " WHERE                                                  \n";
    $stSQL .= "     PE.cod_evento       = PAE.cod_evento       and     \n";
    $stSQL .= "     PA.cod_assentamento = PAE.cod_assentamento and     \n";
    $stSQL .= "     PA.timestamp        = PAE.timestamp                \n";

    return $stSQL;
}

}
