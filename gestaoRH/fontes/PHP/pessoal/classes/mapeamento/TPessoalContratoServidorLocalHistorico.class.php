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
    * Classe de mapeamento da tabela pessoal.contrato_servidor_local_historico
    * Data de Criação: 27/05/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-tabelas

    $Id: TPessoalContratoServidorLocalHistorico.class.php 30566 2008-06-27 13:50:23Z domluc $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.contrato_servidor_local_historico
  * Data de Criação: 27/05/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalContratoServidorLocalHistorico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalContratoServidorLocalHistorico()
{
    parent::Persistente();
    $this->setTabela("pessoal.contrato_servidor_local_historico");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_local,timestamp,cod_contrato');

    $this->AddCampo('cod_local'   ,'integer'      ,true  ,'',true,'TOrganogramaLocal');
    $this->AddCampo('timestamp'   ,'timestamp'    ,true  ,'',true,false);
    $this->AddCampo('cod_contrato','integer'      ,true  ,'',true,'TPessoalContratoServidor');

}
}
?>
