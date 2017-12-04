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

/*
    * Classe de mapeamento da tabela tcmgo.patrimonio_bem_obra
    * Data de Criação   : 28/01/2015

    * @author Analista      Ane
    * @author Desenvolvedor Carlos Adriano

    * @package URBEM
    * @subpackage

    $Id: TTGOPatrimonioBemObra.class.php 61522 2015-01-29 18:33:35Z carlos.silva $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOPatrimonioBemObra extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTGOPatrimonioBemObra()
{
    parent::Persistente();
    $this->setTabela("tcmgo.patrimonio_bem_obra");

    $this->setCampoCod('cod_bem');
    $this->setComplementoChave('cod_obra, ano_obra');

    $this->AddCampo( 'cod_bem'  , 'integer', true , '' , true  , true  );
    $this->AddCampo( 'cod_obra' , 'integer', true , '' , true  , true  );
    $this->AddCampo( 'ano_obra' , 'integer', true , '' , true  , true  );
}

}