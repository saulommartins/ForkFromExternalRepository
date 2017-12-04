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
    * Classe de mapeamento da tabela pessoal.assentamento_evento_proporcional
    * Data de Criação: 26/04/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.assentamento_evento_proporcional
  * Data de Criação: 26/04/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAssentamentoEventoProporcional extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamentoEventoProporcional()
{
    parent::Persistente();
    $this->setTabela("pessoal.assentamento_evento_proporcional");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento,timestamp,cod_evento');

    $this->AddCampo('cod_assentamento'  ,'integer'  ,true   ,'',true,true);
    $this->AddCampo('timestamp'         ,'timestamp',false  ,'',true,true);
    $this->AddCampo('cod_evento'        ,'integer'  ,true   ,'',true,true);

}

function montaRecuperaRelacionamento()
{
    $stSQL .= "SELECT evento.*                                                                  \n";
    $stSQL .= "  FROM ( SELECT cod_assentamento                                                 \n";
    $stSQL .= "              , max(timestamp) as timestamp                                      \n";
    $stSQL .= "           FROM pessoal.assentamento                                             \n";
    $stSQL .= "       GROUP BY cod_assentamento) as max_assentamento                            \n";
    $stSQL .= "     , pessoal.assentamento_evento_proporcional                                  \n";
    $stSQL .= "     , folhapagamento.evento                                                     \n";
    $stSQL .= " WHERE evento.cod_evento       = assentamento_evento_proporcional.cod_evento     \n";
    $stSQL .= "   AND assentamento_evento_proporcional.cod_assentamento = max_assentamento.cod_assentamento  \n";
    $stSQL .= "   AND assentamento_evento_proporcional.timestamp        = max_assentamento.timestamp         \n";

    return $stSQL;
}

}
