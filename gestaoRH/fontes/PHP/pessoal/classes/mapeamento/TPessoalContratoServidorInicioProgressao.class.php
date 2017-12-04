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
    * Classe de mapeamento da tabela PESSOAL.CONTRATO_SERVIDOR_INICIO_PROGRESSAO
    * Data de Criação: 08/09/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CONTRATO_SERVIDOR_INICIO_PROGRESSAO
  * Data de Criação: 08/09/2005

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorInicioProgressao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorInicioProgressao()
{
    parent::Persistente();
    $this->setTabela('pessoal.contrato_servidor_inicio_progressao');

    $this->setCampoCod('');
    $this->setComplementoChave('timestamp,cod_contrato');

    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('cod_contrato','integer',true,'',true,true);
    $this->AddCampo('dt_inicio_progressao','date',true,'',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT to_char(inicio_progressao.dt_inicio_progressao,'dd/mm/yyyy') as dt_inicio_progressao  \n";
    $stSql .= "  FROM pessoal.contrato_servidor_inicio_progressao as inicio_progressao  \n";
    $stSql .= "     , (  SELECT cod_contrato                                            \n";
    $stSql .= "               , max(timestamp) as timestamp                             \n";
    $stSql .= "            FROM pessoal.contrato_servidor_inicio_progressao             \n";
    $stSql .= "        GROUP BY cod_contrato) as max_inicio_progressao                  \n";
    $stSql .= " WHERE inicio_progressao.cod_contrato = max_inicio_progressao.cod_contrato \n";
    $stSql .= "   AND inicio_progressao.timestamp    = max_inicio_progressao.timestamp  \n";

    return $stSql;
}

}
