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
* Arquivo de select Multiplo entidade usuário
* Data de Criação: 01/06/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Fernando Zank Correa Evangelista

* @package URBEM
* @subpackage

$Revision: 30824 $
$Name$
$Author: fernando $
$Date: 2006-09-04 14:16:13 -0300 (Seg, 04 Set 2006) $

$Id: ISelectMultiploEntidadeUsuario.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-02.01.02
*/
include_once ( CLA_SELECT_MULTIPLO );

class  ISelectMultiploEntidadeUsuario extends SelectMultiplo
{

    public $obREntidade;

    public function ISelectMultiploEntidadeUsuario()
    {

        include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                     );
        $this->obREntidade = new ROrcamentoEntidade();
        $this->obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );

        parent::SelectMultiplo();

        $this->setName   ('inCodEntidade');
        $this->setRotulo ( "Entidades" );
        $this->setTitle  ( "Selecione a(s) entidade(s)." );
        $this->setNull   ( false );

        $this->setNomeLista1 ('inCodEntidadeDisponivel');
        $this->setCampoId1   ( 'cod_entidade' );
        $this->setCampoDesc1 ( 'nom_cgm' );

        $this->setNomeLista2 ('inCodEntidade');
        $this->setCampoId2   ('cod_entidade');
        $this->setCampoDesc2 ('nom_cgm');

    }
    public function montaHTML()
    {

        $this->obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );
        $arNomFiltro = Sessao::read('filtroNomRelatorio');
         while ( !$rsEntidades->eof() ) {
           $arNomFiltro['entidade'][$rsEntidades->getCampo( 'cod_entidade' )] = $rsEntidades->getCampo( 'nom_cgm' );
           $rsEntidades->proximo();
       }
       $rsEntidades->setPrimeiroElemento();

        $rsRecordset = new RecordSet();
        if ($rsEntidades->getNumLinhas()==1) {
               $rsRecordset = $rsEntidades;
               $rsEntidades =  new RecordSet;
        }
        $this->SetRecord1    ( $rsEntidades );
        $this->SetRecord2    ( $rsRecordset );

        Sessao::write('filtroNomRelatorio', $arNomFiltro);

        parent::montaHTML();
    }
    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente( $this );
    }
}
?>
