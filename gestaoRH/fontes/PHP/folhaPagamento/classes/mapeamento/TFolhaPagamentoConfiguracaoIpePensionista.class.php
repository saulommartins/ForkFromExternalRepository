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
    * Classe de mapeamento da tabela folhapagamento.configuracao_ipe_pensionista
    * Data de Criação: 03/07/2008

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.66

    $Id: TFolhaPagamentoConfiguracaoIpePensionista.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.configuracao_ipe_pensionista
  * Data de Criação: 03/07/2008

  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoConfiguracaoIpePensionista extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TFolhaPagamentoConfiguracaoIpePensionista()
    {
        parent::Persistente();
        $this->setTabela("folhapagamento.configuracao_ipe_pensionista");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_configuracao,vigencia');

        $this->AddCampo('cod_configuracao' ,'integer',true  ,'',true,'TFolhaPagamentoConfiguracaoIpe');
        $this->AddCampo('vigencia'         ,'date'   ,true  ,'',true,'TFolhaPagamentoConfiguracaoIpe');
        $this->AddCampo('cod_atributo_data','integer',true  ,'',false,'TAdministracaoAtributoDinamico','cod_atributo');
        $this->AddCampo('cod_modulo_data'  ,'integer',true  ,'',false,'TAdministracaoAtributoDinamico','cod_modulo');
        $this->AddCampo('cod_cadastro_data','integer',true  ,'',false,'TAdministracaoAtributoDinamico','cod_cadastro');
        $this->AddCampo('cod_atributo_mat' ,'integer',true  ,'',false,true);
        $this->AddCampo('cod_modulo_mat'   ,'integer',true  ,'',false,true);
        $this->AddCampo('cod_cadastro_mat' ,'integer',true  ,'',false,true);

    }
}
?>
