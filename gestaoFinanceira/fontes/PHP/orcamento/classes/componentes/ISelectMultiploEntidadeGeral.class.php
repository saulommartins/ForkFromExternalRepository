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
* Arquivo de select de Entidades Geral
* Data de Criação: 01/06/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Fernando Zank Correa Evangelista

* @package URBEM
* @subpackage

$Revision: 30824 $
$Name$
$Author: cako $
$Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

* Casos de uso: uc-02.01.02
                uc-06.02.11
                uc-06.02.12
                uc-06.02.13
                uc-06.02.15
                uc-06.02.17
                uc-06.02.18

*/

/*
$Log$
Revision 1.6  2006/07/21 16:40:58  rodrigo
Correções nos casos de uso no codigo
Foram adicionados :
 uc-06.02.11, uc-06.02.12, uc-06.02.13, uc-06.02.15,u c-06.02.17, uc-06.02.18

Revision 1.5  2006/07/05 20:41:48  cleisson
Adicionada tag Log aos arquivos

*/

include_once ( CLA_SELECT_MULTIPLO );

class  ISelectMultiploEntidadeGeral extends SelectMultiplo
{

    public $obTEntidade;

    public function ISelectMultiploEntidadeGeral()
    {

        include_once ( TORC."TOrcamentoEntidade.class.php"    );
        $this->obTEntidade = new TOrcamentoEntidade();

        parent::SelectMultiplo();

        $this->setName   ('inCodEntidade');
        $this->setRotulo ( "Entidades" );
        $this->setTitle  ( "Selecione a(s) entidade(s)." );
        $this->setNull   ( false );

        $this->SetNomeLista1 ('inNumCGMDisponivel');
        $this->setCampoId1   ( 'numcgm' );
        $this->setCampoDesc1 ( 'nom_cgm' );

        $this->SetNomeLista2 ('inNumCGM');
        $this->setCampoId2   ('numcgm');
        $this->setCampoDesc2 ('nom_cgm');

    }
    public function montaHTML()
    {

        $this->obTEntidade->setDado( "exercicio", Sessao::getExercicio() );
        $this->obTEntidade->recuperaEntidadeGeral( $rsEntidadesGeral  );
        $rsRecordset = new RecordSet();
        if ($rsEntidadesGeral->getNumLinhas()==1) {
               $rsRecordset = $rsEntidadesGeral;
               $rsEntidadesGeral = new RecordSet;
        }
        $this->SetRecord1    ( $rsEntidadesGeral );
        $this->SetRecord2    ( $rsRecordset );
        parent::montaHTML();
    }
    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente( $this );
    }
}
?>
