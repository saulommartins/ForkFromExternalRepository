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
    * Classe de mapeamento da tabela STN.VINCULO_RECURSO_ACAO
    * Data de Criação: 16/08/2016

    * @author Analista: Ane Caroline Fiegenbaum Pereira
    * @author Desenvolvedor: Michel Teixeira

    * $Id: TSTNVinculoRecursoAcao.class.php 66353 2016-08-16 20:04:08Z michel $

    * Casos de uso:

*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
require_once CLA_PERSISTENTE;

class TSTNVinculoRecursoAcao extends Persistente
{
    /**
        * Método Construtor
    */
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('stn.vinculo_recurso_acao');

        $this->setCampoCod('');
        $this->setComplementoChave( 'exercicio, cod_entidade, num_orgao, num_unidade, cod_recurso, cod_vinculo, cod_acao, cod_tipo_educacao' );

        $this->AddCampo( 'exercicio'         , 'char'   , true, '04', true , true );
        $this->AddCampo( 'cod_entidade'      , 'integer', true,   '', true , true );
        $this->AddCampo( 'num_orgao'         , 'integer', true,   '', true , true );
        $this->AddCampo( 'num_unidade'       , 'integer', true,   '', true , true );
        $this->AddCampo( 'cod_recurso'       , 'integer', true,   '', true , true );
        $this->AddCampo( 'cod_vinculo'       , 'integer', true,   '', false, true );
        $this->AddCampo( 'cod_tipo'          , 'integer', true,   '', true , true );
        $this->AddCampo( 'cod_tipo_educacao' , 'integer', true,   '', true , true );
        $this->AddCampo( 'cod_acao'          , 'integer', true,   '', true , true );
    }

}

?>
