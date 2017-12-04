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
    * Classe de mapeamento da tabela ima.configuracao_banpara_orgao
    * Data de Criação: 01/09/2009

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-tabelas

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ima.configuracao_banpara_lotacao
  * Data de Criação: 01/09/2009

  * @author Desenvolvedor: Rafael Garbin

  * @package URBEM
  * @subpackage Mapeamento
*/
class TIMAConfiguracaoBanparaOrgao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TIMAConfiguracaoBanparaOrgao()
    {
        parent::Persistente();
        $this->setTabela("ima.configuracao_banpara_orgao");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_empresa,num_orgao_banpara,timestamp,cod_orgao');

        $this->AddCampo('cod_empresa'      , 'integer'      , true, '', true, 'TIMAConfiguracaoBanpara');
        $this->AddCampo('num_orgao_banpara', 'integer'      , true, '', true, 'TIMAConfiguracaoBanpara');
        $this->AddCampo('timestamp'        , 'timestamp_now', true, '', true, 'TIMAConfiguracaoBanpara');
        $this->AddCampo('cod_orgao'        , 'integer'      , true, '', true, 'TOrganogramaOrgao');
    }
}
?>
