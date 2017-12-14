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
 * Classe de mapeamento da tabela ppa.acao_meta_fisica_realizada
 * Data de Criação: 15/04/2016

 * @author Analista      : Valtair Santos
 * @author Desenvolvedor : Michel Teixeira

 * @package URBEM
 * @subpackage Mapeamento

 $Id: TPPAAcaoMetaFisicaRealizada.class.php 64971 2016-04-15 18:54:14Z michel $

**/

class TPPAAcaoMetaFisicaRealizada extends Persistente
{
    /**
     * Método construtor
     * @access private
    **/
    public function __construct()
    {
        parent::Persistente();

        $this->setTabela('ppa.acao_meta_fisica_realizada');

        $this->setCampoCod('');
        $this->setComplementoChave('cod_acao,timestamp_acao_dados,ano,cod_recurso,exercicio_recurso');

        $this->addCampo('cod_acao'            , 'integer'  , true , ''    , true , true );
        $this->addCampo('timestamp_acao_dados', 'timestamp', true , ''    , true , true );
        $this->addCampo('ano'                 , 'character', true , '1'   , true , true );
        $this->addCampo('valor'               , 'numeric'  , true , '14,2', false, false);
        $this->addCampo('justificativa'       , 'varchar'  , false, '255' , false, false);
        $this->addCampo('cod_recurso'         , 'integer'  , true , ''    , true , true );
        $this->addCampo('exercicio_recurso'   , 'char'     , true , '4'   , true , true );
    }

}

?>
